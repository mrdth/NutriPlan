<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Edit Recipe</h1>
      </div>
    </template>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <p>Edit Recipe Form Coming Soon</p>
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
