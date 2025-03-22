<template>
  <AppLayout>
    <Head title="Create New Recipe | NutriPlan" />

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
          <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">Create New Recipe</h1>
          <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Add a new recipe to your collection</p>
        </div>
      </div>

      <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
        <RecipeForm
          :categories="categories"
          :ingredients="ingredients"
          :measurement-units="measurementUnits"
          submit-label="Create Recipe"
          @submit="createRecipe"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import RecipeForm from '@/components/Recipe/RecipeForm.vue'
import type { Category } from '@/types/category'
import type { Ingredient } from '@/types/ingredient'

interface Props {
  categories: Category[]
  ingredients: Ingredient[]
  measurementUnits: Array<{ value: string; label: string }>
}

defineProps<Props>()

const createRecipe = (form: any) => {
  form.post(route('recipes.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
    },
  })
}
</script>
