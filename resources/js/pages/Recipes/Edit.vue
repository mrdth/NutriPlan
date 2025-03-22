<script setup lang="ts">
import RecipeForm from '@/components/Recipe/RecipeForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Recipe } from '@/types/recipe';
import { Head } from '@inertiajs/vue3';

defineProps<{
    recipe: Recipe;
    categories: Array<{
        id: number;
        name: string;
    }>;
    ingredients: Array<{
        id: number;
        name: string;
    }>;
    measurementUnits: Array<{
        value: string;
        label: string;
    }>;
}>();
</script>

<template>
    <AppLayout>
        <Head :title="`Edit ${recipe.title}`" />

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">Edit Recipe</h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Update your recipe details below.</p>
                </div>
            </div>

            <div class="mt-8">
                <RecipeForm
                    :recipe="recipe"
                    :categories="categories"
                    :ingredients="ingredients"
                    :measurement-units="measurementUnits"
                    :action="route('recipes.update', recipe.id)"
                    method="put"
                    @submit="form => form.put(route('recipes.update', recipe.slug))"
                />
            </div>
        </div>
    </AppLayout>
</template>
