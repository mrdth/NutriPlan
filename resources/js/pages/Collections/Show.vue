<template>
    <AppLayout>
        <Head :title="`${collection.name} | NutriPlan`" />

        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">{{ collection.name }}</h1>
                    <p v-if="collection.description" class="mt-2 text-sm text-gray-700 dark:text-gray-400">
                        {{ collection.description }}
                    </p>
                </div>
                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <Button variant="outline" @click="editCollection">
                        <PencilIcon class="mr-2 h-4 w-4" />
                        Edit Collection
                    </Button>
                </div>
            </div>

            <div v-if="collection.recipes.length === 0" class="mt-8 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <UtensilsCrossedIcon class="h-12 w-12" />
                </div>
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No recipes in this collection</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add recipes to this collection to get started.</p>
                <div class="mt-6">
                    <Link :href="route('recipes.index')">
                        <Button>
                            <PlusIcon class="mr-2 h-4 w-4" />
                            Browse Recipes
                        </Button>
                    </Link>
                </div>
            </div>

            <div v-else class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="recipe in collection.recipes" :key="recipe.id" class="relative">
                    <RecipeCard :recipe="recipe" />
                    <Button variant="ghost" size="icon" class="absolute right-2 top-2" @click="removeRecipe(recipe)">
                        <XIcon class="h-5 w-5" />
                    </Button>
                </div>
            </div>
        </div>

        <!-- Edit Collection Modal -->
        <Dialog :open="isEditModalOpen" @update:open="isEditModalOpen = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Edit Collection</DialogTitle>
                    <DialogDescription> Update your collection details. </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="saveCollection">
                    <div class="space-y-4 py-4">
                        <div class="space-y-2">
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="form.description" />
                            <InputError :message="form.errors.description" />
                        </div>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="isEditModalOpen = false">Cancel</Button>
                        <Button type="submit" :disabled="form.processing">Update</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Remove Recipe Confirmation Modal -->
        <Dialog :open="isRemoveModalOpen" @update:open="isRemoveModalOpen = $event">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Remove Recipe</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to remove this recipe from the collection? This will not delete the recipe itself.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="isRemoveModalOpen = false">Cancel</Button>
                    <Button type="button" variant="destructive" @click="confirmRemoveRecipe">Remove</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import RecipeCard from '@/components/Recipe/RecipeCard.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { InputError } from '@/components/ui/input-error';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PencilIcon, PlusIcon, UtensilsCrossedIcon, XIcon } from 'lucide-vue-next';
import { ref } from 'vue';
// Define Recipe type locally to match the RecipeCard component's expectations
interface Recipe {
    id: number;
    title: string;
    description: string | null;
    slug: string;
    prep_time: number;
    cooking_time: number;
    servings: number;
    images: string[];
    url: string | null;
    user: {
        name: string;
    };
    categories: {
        id: number;
        name: string;
        slug: string;
        recipe_count: number;
    }[];
}

interface Collection {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    recipes: Recipe[];
}

interface Props {
    collection: Collection;
}

const props = defineProps<Props>();

const isEditModalOpen = ref(false);
const isRemoveModalOpen = ref(false);
const recipeToRemove = ref<Recipe | null>(null);

const form = useForm({
    name: props.collection.name,
    description: props.collection.description || '',
});

const editCollection = () => {
    form.name = props.collection.name;
    form.description = props.collection.description || '';
    isEditModalOpen.value = true;
};

const saveCollection = () => {
    form.put(route('collections.update', props.collection.slug), {
        onSuccess: () => {
            isEditModalOpen.value = false;
        },
    });
};

const removeRecipe = (recipe: Recipe) => {
    recipeToRemove.value = recipe;
    isRemoveModalOpen.value = true;
};

const confirmRemoveRecipe = () => {
    if (recipeToRemove.value) {
        useForm({}).delete(
            route('collections.remove-recipe', {
                collection: props.collection.slug,
                recipe: recipeToRemove.value.slug,
            }),
            {
                onSuccess: () => {
                    isRemoveModalOpen.value = false;
                    recipeToRemove.value = null;
                },
            },
        );
    }
};
</script>
