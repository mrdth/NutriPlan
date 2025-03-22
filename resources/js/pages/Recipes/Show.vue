<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">{{ recipe.title }}</h1>
      </div>
    </template>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <div class="prose max-w-none">
          <p class="text-gray-600">{{ recipe.description }}</p>

          <div class="mt-6">
            <h2 class="text-xl font-semibold mb-4">Details</h2>
            <div class="grid grid-cols-3 gap-4">
              <div>
                <p class="text-gray-600">Prep Time</p>
                <p class="font-medium">{{ recipe.prep_time }} minutes</p>
              </div>
              <div>
                <p class="text-gray-600">Cooking Time</p>
                <p class="font-medium">{{ recipe.cooking_time }} minutes</p>
              </div>
              <div>
                <p class="text-gray-600">Servings</p>
                <p class="font-medium">{{ recipe.servings }}</p>
              </div>
            </div>
          </div>

          <div class="mt-8">
            <h2 class="text-xl font-semibold mb-4">Ingredients</h2>
            <ul class="list-disc list-inside">
              <li v-for="ingredient in recipe.ingredients" :key="ingredient.id">
                {{ ingredient.pivot.amount }} {{ ingredient.pivot.unit }} {{ ingredient.name }}
              </li>
            </ul>
          </div>

          <div class="mt-8">
            <h2 class="text-xl font-semibold mb-4">Instructions</h2>
            <div class="whitespace-pre-wrap">{{ recipe.instructions }}</div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import type { Recipe } from '@/types/recipe'

defineProps<{
  recipe: Recipe
}>()
</script>
