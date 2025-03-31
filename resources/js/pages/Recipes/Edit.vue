<script setup lang="ts">
import DeleteRecipeModal from '@/components/Recipe/DeleteRecipeModal.vue';
import RecipeForm from '@/components/Recipe/RecipeForm.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Recipe } from '@/types/recipe';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

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

const showDeleteModal = ref(false);
</script>

<template>
    <AppLayout>
        <Head :title="`Edit ${recipe.title}`" />

        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">Edit Recipe</h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Update your recipe details below.</p>
                </div>
                <div class="mt-4 sm:ml-4 sm:mt-0">
                    <DeleteRecipeModal v-model:open="showDeleteModal" :recipe-slug="recipe.slug">
                        <Button variant="destructive">Delete Recipe</Button>
                    </DeleteRecipeModal>
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
                    @submit="(form) => form.put(route('recipes.update', recipe.slug))"
                />
            </div>
        </div>
    </AppLayout>
</template>
