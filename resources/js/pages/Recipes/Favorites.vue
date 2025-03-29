<template>
    <AppLayout>
        <Head title="Favorite Recipes" />

        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">Favorite Recipes</h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Browse through your favorite recipes</p>
                </div>
            </div>

            <div v-if="favorites.data.length === 0" class="mt-16 text-center">
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No favorite recipes</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add recipes to your favorites to see them here</p>
                <div class="mt-6">
                    <Link :href="route('recipes.index')">
                        <Button>
                            <ArrowLeftIcon class="mr-2 h-4 w-4" />
                            Browse Recipes
                        </Button>
                    </Link>
                </div>
            </div>

            <div v-else class="mt-8">
                <div class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
                    <RecipeCard v-for="recipe in favorites.data" :key="recipe.id" :recipe="recipe" />
                </div>

                <div class="mt-8">
                    <Pagination :links="favorites.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import RecipeCard from '@/components/Recipe/RecipeCard.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeftIcon } from 'lucide-vue-next';

interface Props {
    favorites: {
        data: Array<{
            id: number;
            title: string;
            description: string | null;
            slug: string;
            prep_time: number;
            cooking_time: number;
            servings: number;
            images: string[];
            url: string | null;
            is_favorited: boolean;
            user: {
                name: string;
            };
            categories: Array<{
                id: number;
                name: string;
                slug: string;
                recipe_count: number;
            }>;
        }>;
        links: Array<{
            url?: string;
            label: string;
            active: boolean;
        }>;
    };
}

defineProps<Props>();
</script>
