<template>
    <form @submit.prevent="submit" class="space-y-8">
        <div class="space-y-6">
            <!-- Title -->
            <div>
                <Label for="title">Title</Label>
                <Input id="title" v-model="form.title" type="text" class="mt-1" required />
                <InputError :message="form.errors.title" />
            </div>

            <!-- Description -->
            <div>
                <Label for="description">Description</Label>
                <Textarea id="description" v-model="form.description" class="mt-1 p-3" />
                <InputError :message="form.errors.description" />
            </div>

            <!-- Times and Servings -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div>
                    <Label for="prep_time">Prep Time (minutes)</Label>
                    <Input id="prep_time" v-model.number="form.prep_time" type="number" min="1" class="mt-1" required />
                    <InputError :message="form.errors.prep_time" />
                </div>

                <div>
                    <Label for="cooking_time">Cooking Time (minutes)</Label>
                    <Input id="cooking_time" v-model.number="form.cooking_time" type="number" min="1" class="mt-1" required />
                    <InputError :message="form.errors.cooking_time" />
                </div>

                <div>
                    <Label for="servings">Servings</Label>
                    <Input id="servings" v-model.number="form.servings" type="number" min="1" class="mt-1" required />
                    <InputError :message="form.errors.servings" />
                </div>
            </div>

            <!-- Categories -->
            <div>
                <Label>Categories</Label>
                <div class="mt-1 flex flex-wrap gap-2">
                    <Badge
                        v-for="category in selectedCategories"
                        :key="category.id"
                        variant="outline"
                        class="cursor-pointer bg-primary text-primary-foreground hover:bg-primary/90"
                        @click="toggleCategory(category.id)"
                    >
                        {{ category.name }}
                        <XIcon class="ml-1 h-3 w-3" />
                    </Badge>
                </div>

                <div class="mt-2">
                    <Combobox
                        :model-value="0"
                        :options="availableCategories"
                        placeholder="Search for categories..."
                        @update:model-value="addCategory"
                    />
                </div>
                <InputError :message="form.errors.categories" />
            </div>

            <!-- Ingredients -->
            <div>
                <Label>Ingredients</Label>
                <div class="mt-4 space-y-4">
                    <div v-for="(ingredient, index) in form.ingredients" :key="index" class="flex items-end gap-4">
                        <div class="flex-1">
                            <Label :for="'ingredient-' + index">Ingredient</Label>
                            <ComboboxWithCreate
                                :id="'ingredient-' + index"
                                v-model="ingredient.ingredient_id"
                                :options="ingredients"
                                :selected="ingredients.find((i) => i.id === ingredient.ingredient_id)"
                                :allow-create="true"
                                create-endpoint="/ingredients"
                                class="mt-1"
                                @option-created="handleNewIngredient"
                            />
                            <InputError :message="form.errors['ingredients.' + index + '.ingredient_id']" />
                        </div>

                        <div class="w-24">
                            <Label :for="'amount-' + index">Amount</Label>
                            <Input
                                :id="'amount-' + index"
                                v-model.number="ingredient.amount"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-1"
                                required
                            />
                            <InputError :message="form.errors['ingredients.' + index + '.amount']" />
                        </div>

                        <div class="w-32">
                            <Label :for="'unit-' + index">Unit</Label>
                            <Select :id="'unit-' + index" v-model="ingredient.unit" :options="measurementUnits" class="mt-1" :allow-empty="true" />
                            <InputError :message="form.errors['ingredients.' + index + '.unit']" />
                        </div>

                        <Button type="button" variant="destructive" size="icon" @click="removeIngredient(index)">
                            <TrashIcon class="h-4 w-4" />
                        </Button>
                    </div>

                    <Button type="button" variant="outline" @click="addIngredient">
                        <PlusIcon class="mr-2 h-4 w-4" />
                        Add Ingredient
                    </Button>
                </div>
            </div>

            <!-- Instructions -->
            <div>
                <Label for="instructions">Instructions</Label>
                <Textarea id="instructions" v-model="form.instructions" class="mt-1 p-3" required rows="10" />
                <InputError :message="form.errors.instructions" />
            </div>

            <!-- Images -->
            <div>
                <Label>Images</Label>
                <div class="mt-1">
                    <FileInput v-model="form.images" multiple accept="image/*" />
                </div>
                <InputError :message="form.errors.images" />
            </div>

            <!-- Publishing -->
            <div class="flex items-center gap-4">
                <Checkbox id="publish" v-model="form.published_at" :true-value="new Date().toISOString()" :false-value="null" />
                <Label for="publish">Publish immediately</Label>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <Button type="button" variant="outline" @click="() => form.get(route('recipes.index'))">Cancel</Button>
            <Button type="submit" :disabled="form.processing">{{ submitLabel }}</Button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Combobox, ComboboxWithCreate } from '@/components/ui/combobox';
import { FileInput } from '@/components/ui/file-input';
import { Input } from '@/components/ui/input';
import { InputError } from '@/components/ui/input-error';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Recipe } from '@/types/recipe';
import type { FormDataConvertible } from '@inertiajs/core';
import { useForm } from '@inertiajs/vue3';
import { PlusIcon, TrashIcon, XIcon } from 'lucide-vue-next';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        recipe?: Recipe;
        submitLabel?: string;
        categories: Array<{ id: number; name: string }>;
        ingredients: Array<{ id: number; name: string }>;
        measurementUnits: Array<{ value: string; label: string }>;
    }>(),
    {
        submitLabel: 'Save Recipe',
    },
);

interface FormData {
    title: string;
    description: string;
    instructions: string;
    prep_time: number;
    cooking_time: number;
    servings: number;
    categories: number[];
    ingredients: Array<{
        ingredient_id: number;
        amount: number;
        unit: string | null;
    }>;
    images: File[];
    published_at: string | null;
    [key: string]: FormDataConvertible;
}

const form = useForm<FormData>({
    title: props.recipe?.title ?? '',
    description: props.recipe?.description ?? '',
    instructions: props.recipe?.instructions ?? '',
    prep_time: props.recipe?.prep_time ?? 30,
    cooking_time: props.recipe?.cooking_time ?? 30,
    servings: props.recipe?.servings ?? 4,
    categories: props.recipe?.categories?.map((c) => c.id) ?? [],
    ingredients:
        props.recipe?.ingredients?.map((i) => ({
            ingredient_id: i.id,
            amount: i.pivot.amount,
            unit: i.pivot.unit,
        })) ?? [],
    images: [] as File[],
    published_at: props.recipe?.status === 'published' ? new Date().toISOString() : null,
});

const toggleCategory = (id: number) => {
    const index = form.categories.indexOf(id);
    if (index !== -1) {
        form.categories.splice(index, 1);
    }
};

const addCategory = (id: number) => {
    if (id === 0) return; // Skip if no category is selected
    if (!form.categories.includes(id)) {
        form.categories.push(id);
    }
};

const addIngredient = () => {
    form.ingredients.push({
        ingredient_id: 0,
        amount: 1,
        unit: '',
    });
};

const removeIngredient = (index: number) => {
    form.ingredients.splice(index, 1);
};

const handleNewIngredient = (newIngredient: { id: number; name: string }) => {
    // Add the new ingredient to the ingredients list
    props.ingredients.push(newIngredient);
};

const emit = defineEmits<{
    (e: 'submit', form: ReturnType<typeof useForm<FormData>>): void;
}>();

const submit = () => {
    emit('submit', form);
};

const selectedCategories = computed(() => {
    return props.categories.filter((category) => form.categories.includes(category.id));
});

const availableCategories = computed(() => {
    return props.categories.filter((category) => !form.categories.includes(category.id));
});
</script>
