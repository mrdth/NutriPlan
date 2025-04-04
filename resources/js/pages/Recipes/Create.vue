<template>
    <AppLayout>

        <Head title="Create New Recipe | NutriPlan" />

        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">Create New Recipe</h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Add a new recipe to your collection</p>
                </div>
            </div>

            <div class="mt-8 overflow-hidden bg-white p-6 shadow-xl dark:bg-gray-800 sm:rounded-lg">
                <RecipeForm :categories="categories" :ingredients="ingredients" :measurement-units="measurementUnits"
                    submit-label="Create Recipe" @submit="createRecipe" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import RecipeForm from '@/components/Recipe/RecipeForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Category } from '@/types/category';
import type { Ingredient } from '@/types/ingredient';
import type { MeasurementUnit } from '@/types/recipe';
import { Head } from '@inertiajs/vue3';

interface Props {
    categories: Category[];
    ingredients: Ingredient[];
    measurementUnits: MeasurementUnit[];
}

defineProps<Props>();

const createRecipe = (form: any) => {
    form.post(route('recipes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>
