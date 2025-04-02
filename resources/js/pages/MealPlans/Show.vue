<template>
    <AppLayout>

        <Head :title="`${mealPlan.name || 'Meal Plan'} | NutriPlan`" />

        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ mealPlan.name || `Meal Plan (${formatStartDate(mealPlan.start_date)})` }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">
                        {{ formatStartDate(mealPlan.start_date) }} to {{ formatEndDate(mealPlan.start_date,
                            mealPlan.duration) }} •
                        {{ mealPlan.people_count }} people
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <Button variant="destructive" size="sm" @click="confirmDeleteMealPlan">
                        <TrashIcon class="mr-2 h-4 w-4" />
                        Delete
                    </Button>
                </div>
            </div>

            <div class="rounded-lg border dark:border-gray-800">
                <div class="p-6">
                    <div class="mb-6 flex justify-between">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recipes</h2>
                        <Button @click="showAddRecipeModal = true">
                            <PlusIcon class="mr-2 h-4 w-4" />
                            Add Recipe
                        </Button>
                    </div>

                    <div v-if="mealPlan.recipes && mealPlan.recipes.length > 0" class="space-y-4">
                        <div v-for="recipe in mealPlan.recipes" :key="recipe.id"
                            class="flex items-center justify-between rounded-md border p-4 dark:border-gray-700">
                            <div class="flex items-center gap-4">
                                <div v-if="recipe.images && recipe.images.length > 0"
                                    class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md">
                                    <img :src="recipe.images[0]" alt="" class="h-full w-full object-cover" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        <Link :href="route('recipes.show', recipe.slug)" class="hover:underline">
                                        {{ recipe.title }}
                                        </Link>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Scale Factor: {{ formatScaleFactor(recipe.pivot.scale_factor) }}x ({{
                                            calculateServings(recipe.servings, recipe.pivot.scale_factor)
                                        }}
                                        servings)
                                        <span class="ml-2 text-green-600 dark:text-green-400">
                                            • {{ formatScaleFactor(recipe.pivot.servings_available) }} available
                                            servings
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <Button variant="ghost" size="sm" @click="editRecipeInPlan(recipe)">
                                    <PencilIcon class="h-4 w-4" />
                                </Button>
                                <Button variant="ghost" size="sm" @click="confirmRemoveRecipe(recipe)">
                                    <XIcon class="h-4 w-4 text-red-500" />
                                </Button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="rounded-md bg-gray-50 p-4 dark:bg-gray-800">
                        <p class="text-center text-gray-700 dark:text-gray-300">
                            No recipes added to this meal plan yet. Click "Add Recipe" to get started.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Dialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Delete Meal Plan</DialogTitle>
                    <DialogDescription> Are you sure you want to delete this meal plan? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <div class="flex items-center justify-end space-x-2 pt-4">
                    <Button variant="outline" @click="showDeleteDialog = false">Cancel</Button>
                    <Button variant="destructive" @click="deleteMealPlan">Delete</Button>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Remove Recipe Confirmation Modal -->
        <Dialog :open="showRemoveRecipeDialog" @update:open="showRemoveRecipeDialog = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Remove Recipe</DialogTitle>
                    <DialogDescription> Are you sure you want to remove "{{ recipeToRemove?.title }}" from this meal
                        plan? </DialogDescription>
                </DialogHeader>
                <div class="flex items-center justify-end space-x-2 pt-4">
                    <Button variant="outline" @click="showRemoveRecipeDialog = false">Cancel</Button>
                    <Button variant="destructive" @click="removeRecipe">Remove</Button>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Add Recipe Modal -->
        <Dialog :open="showAddRecipeModal" @update:open="showAddRecipeModal = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Add Recipe to Meal Plan</DialogTitle>
                    <DialogDescription> Search for a recipe to add to your meal plan. </DialogDescription>
                </DialogHeader>
                <div class="space-y-4 py-4">
                    <div class="space-y-2">
                        <Label for="recipe-search">Search Recipes</Label>
                        <div class="relative">
                            <Input id="recipe-search" v-model="searchQuery" placeholder="Type to search..."
                                @input="debounceSearch" />
                            <div v-if="isSearching" class="absolute right-3 top-2.5">
                                <Spinner class="h-5 w-5 text-gray-400" />
                            </div>
                        </div>
                    </div>

                    <div v-if="searchResults.length > 0"
                        class="max-h-60 overflow-y-auto rounded-md border p-2 dark:border-gray-700">
                        <div v-for="recipe in searchResults" :key="recipe.id"
                            class="cursor-pointer rounded-md p-2 hover:bg-gray-100 dark:hover:bg-gray-800"
                            @click="selectRecipe(recipe)">
                            <div class="flex items-center gap-3">
                                <div v-if="recipe.images && recipe.images.length > 0"
                                    class="h-10 w-10 overflow-hidden rounded-md">
                                    <img :src="recipe.images[0]" alt="" class="h-full w-full object-cover" />
                                </div>
                                <div>
                                    <p class="font-medium">{{ recipe.title }}</p>
                                    <p class="text-xs text-gray-500">{{ recipe.servings }} servings</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="searchQuery && !isSearching && searchResults.length === 0"
                        class="rounded-md bg-gray-50 p-3 dark:bg-gray-800">
                        <p class="text-center text-sm text-gray-500">No recipes found matching your search.</p>
                    </div>

                    <div v-if="selectedRecipe" class="rounded-md border p-3 dark:border-gray-700">
                        <h3 class="font-medium">{{ selectedRecipe.title }}</h3>
                        <div class="mt-3 space-y-2">
                            <div>
                                <Label for="scale-factor">Scale Factor</Label>
                                <Input id="scale-factor" v-model.number="scaleFactor" type="number" min="0.5" max="10"
                                    step="0.5" />
                                <p class="mt-1 text-xs text-gray-500">
                                    This will make approximately {{ calculateServings(selectedRecipe.servings,
                                        scaleFactor) }} servings
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="showAddRecipeModal = false">Cancel</Button>
                    <Button :disabled="!selectedRecipe" @click="addRecipeToMealPlan"> Add Recipe </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Edit Recipe Scale Factor Modal -->
        <Dialog :open="showEditRecipeModal" @update:open="showEditRecipeModal = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Edit Scale Factor</DialogTitle>
                    <DialogDescription> Adjust the scale factor for "{{ recipeToEdit?.title }}" </DialogDescription>
                </DialogHeader>
                <div class="space-y-4 py-4">
                    <div>
                        <Label for="edit-scale-factor">Scale Factor</Label>
                        <Input id="edit-scale-factor" v-model.number="editScaleFactor" type="number" min="0.5" max="10"
                            step="0.5" />
                        <p class="mt-1 text-xs text-gray-500">
                            This will make approximately {{ recipeToEdit ? calculateServings(recipeToEdit.servings,
                                editScaleFactor) : 0 }} servings
                        </p>
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="showEditRecipeModal = false">Cancel</Button>
                    <Button @click="updateRecipeScaleFactor">Save Changes</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Spinner from '@/components/ui/spinner.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { MealPlan } from '@/types/meal-plan';
import type { Recipe, RecipeWithPivot } from '@/types/recipe';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { PencilIcon, PlusIcon, TrashIcon, XIcon } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    mealPlan: MealPlan & {
        recipes?: Array<
            Recipe & {
                pivot: {
                    scale_factor: number;
                    servings_available: number;
                };
            }
        >;
    };
    availableMealPlans: Array<{
        id: number;
        name: string | null;
        start_date: string;
    }>;
}

const props = defineProps<Props>();

const showDeleteDialog = ref(false);
const showAddRecipeModal = ref(false);
const showRemoveRecipeDialog = ref(false);
const showEditRecipeModal = ref(false);
const recipeToRemove = ref<Recipe | null>(null);
const recipeToEdit = ref<RecipeWithPivot | null>(null);

const searchQuery = ref('');
const searchResults = ref<Recipe[]>([]);
const isSearching = ref(false);
const selectedRecipe = ref<Recipe | null>(null);
const scaleFactor = ref(1.0);
const editScaleFactor = ref(1.0);

const formatStartDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatEndDate = (dateString: string, numDays: number) => {
    const date = new Date(dateString);
    date.setDate(date.getDate() + numDays);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const deleteMealPlan = () => {
    router.delete(route('meal-plans.destroy', props.mealPlan.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
        },
    });
};

const confirmDeleteMealPlan = () => {
    showDeleteDialog.value = true;
};

let searchTimeout: ReturnType<typeof setTimeout>;

const debounceSearch = () => {
    clearTimeout(searchTimeout);
    if (searchQuery.value) {
        isSearching.value = true;
        searchTimeout = setTimeout(() => {
            searchRecipes();
        }, 300);
    } else {
        searchResults.value = [];
        isSearching.value = false;
    }
};

const searchRecipes = async () => {
    try {
        const response = await axios.get(route('api.recipes.search'), {
            params: { query: searchQuery.value },
            withCredentials: true,
        });
        searchResults.value = response.data.data;
    } catch (error) {
        console.error('Error searching recipes:', error);
        searchResults.value = [];
    } finally {
        isSearching.value = false;
    }
};

const selectRecipe = (recipe: Recipe) => {
    selectedRecipe.value = recipe;
    scaleFactor.value = 1.0;
    searchQuery.value = '';
    searchResults.value = [];
};

const calculateServings = (originalServings: number, scaleFactor: number): number => {
    return Math.round(originalServings * scaleFactor);
};

const addRecipeToMealPlan = () => {
    if (!selectedRecipe.value) return;

    router.post(
        route('meal-plans.add-recipe'),
        {
            meal_plan_id: props.mealPlan.id,
            recipe_id: selectedRecipe.value.id,
            scale_factor: scaleFactor.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                showAddRecipeModal.value = false;
                selectedRecipe.value = null;
                scaleFactor.value = 1.0;
            },
        },
    );
};

const confirmRemoveRecipe = (recipe: Recipe) => {
    recipeToRemove.value = recipe;
    showRemoveRecipeDialog.value = true;
};

const removeRecipe = () => {
    if (!recipeToRemove.value) return;

    router.delete(
        route('meal-plans.remove-recipe', {
            id: props.mealPlan.id,
            recipeId: recipeToRemove.value.id,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                showRemoveRecipeDialog.value = false;
                recipeToRemove.value = null;
            },
        },
    );
};

const editRecipeInPlan = (recipe: Recipe & { pivot: { scale_factor: number } }) => {
    recipeToEdit.value = recipe as RecipeWithPivot;
    editScaleFactor.value = recipe.pivot.scale_factor;
    showEditRecipeModal.value = true;
};

const updateRecipeScaleFactor = () => {
    if (!recipeToEdit.value) return;

    // First remove the recipe
    router.delete(
        route('meal-plans.remove-recipe', {
            id: props.mealPlan.id,
            recipeId: recipeToEdit.value.id,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                // Then add it back with the new scale factor
                router.post(
                    route('meal-plans.add-recipe'),
                    {
                        meal_plan_id: props.mealPlan.id,
                        recipe_id: recipeToEdit.value!.id,
                        scale_factor: editScaleFactor.value,
                    },
                    {
                        preserveScroll: true,
                        onSuccess: () => {
                            showEditRecipeModal.value = false;
                            recipeToEdit.value = null;
                        },
                    },
                );
            },
        },
    );
};

const formatScaleFactor = (factor: number | string): string => {
    const num = Number(factor);
    return Number.isInteger(num) ? num.toString() : num.toFixed(1);
};
</script>
