<template>
    <AppLayout>
        <Head :title="`${recipe.title} | NutriPlan`" />

        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">{{ recipe.title }}</h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">
                        Created by {{ recipe.user.name }} on {{ new Date(recipe.created_at).toLocaleDateString() }}
                    </p>
                </div>
                <div v-if="page.props.auth.user.id === recipe.user.id" class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <Link :href="route('recipes.edit', recipe.slug)">
                        <Button>
                            <PencilIcon class="mr-2 h-4 w-4" />
                            Edit Recipe
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="mt-8 overflow-hidden bg-white p-6 shadow-xl dark:bg-gray-800 sm:rounded-lg">
                <!-- Description -->
                <p v-if="recipe.description" class="text-gray-600 dark:text-gray-300">
                    {{ recipe.description }}
                </p>

                <!-- Categories -->
                <div v-if="recipe.categories.length > 0" class="mt-6">
                    <div class="flex flex-wrap gap-2">
                        <Link v-for="category in recipe.categories" :key="category.id" :href="route('categories.show', category.slug)">
                            <Badge variant="secondary" class="cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700">
                                {{ category.name }}
                            </Badge>
                        </Link>
                    </div>
                </div>

                <!-- Details -->
                <div class="mt-8">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">Details</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Prep Time</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ recipe.prep_time }} minutes</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Cooking Time</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ recipe.cooking_time }} minutes</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Servings</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ recipe.servings }}</p>
                        </div>
                    </div>
                </div>

                <!-- Ingredients -->
                <div class="mt-8">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">Ingredients</h2>
                    <ul class="space-y-2">
                        <li v-for="ingredient in recipe.ingredients" :key="ingredient.id" class="flex items-center text-gray-700 dark:text-gray-300">
                            <div class="mr-3 h-1.5 w-1.5 rounded-full bg-gray-600 dark:bg-gray-400" />
                            <span class="font-medium">
                                {{ ingredient.pivot.amount }}
                                <template v-if="ingredient.pivot.unit">{{ ingredient.pivot.unit }}</template>
                            </span>
                            <span class="ml-1">{{ ingredient.name }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Instructions -->
                <div class="mt-8">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">Instructions</h2>
                    <div class="prose prose-gray dark:prose-invert max-w-none" v-html="formatInstructions(recipe.instructions)" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Recipe } from '@/types/recipe';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { PencilIcon } from 'lucide-vue-next';

interface PageProps {
    [key: string]: unknown;
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
        };
    };
}

const page = usePage<PageProps>();

defineProps<{
    recipe: Recipe;
}>();

const formatInstructions = (instructions: string): string => {
    return instructions
        .split('\n')
        .filter((line) => line.trim())
        .map((line) => `<p>${line}</p>`)
        .join('');
};
</script>
