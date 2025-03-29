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
                <div class="mt-4 space-x-2 sm:ml-16 sm:mt-0 sm:flex-none">
                    <!-- Favorite button for all authenticated users -->
                    <Button @click="toggleFavorite" :variant="isFavorited ? 'default' : 'outline'">
                        <HeartIcon class="mr-2 h-4 w-4" :class="{ 'fill-current': isFavorited }" />
                        {{ isFavorited ? 'Unfavorite' : 'Favorite' }}
                    </Button>
                    <!-- Edit button only for recipe creator -->
                    <Link v-if="page.props.auth.user.id === recipe.user.id" :href="route('recipes.edit', recipe.slug)">
                        <Button>
                            <PencilIcon class="mr-2 h-4 w-4" />
                            Edit Recipe
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="mt-8 overflow-hidden bg-white p-6 shadow-xl dark:bg-gray-800 sm:rounded-lg">
                <div class="flex flex-col gap-8 md:flex-row">
                    <!-- Main content column -->
                    <div class="flex-1">
                        <!-- Description -->
                        <p v-if="recipe.description" class="text-gray-600 dark:text-gray-300">
                            {{ recipe.description }}
                        </p>

                        <!-- Details -->
                        <div class="mt-6">
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

                        <!-- Nutrition Information -->
                        <div v-if="recipe.nutrition_information" class="mt-8">
                            <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">Nutrition</h2>
                            <NutritionInformation :nutrition="recipe.nutrition_information" />
                        </div>

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

                        <!-- Scaling Control -->
                        <div class="mt-6">
                            <ScalingControl :original-servings="recipe.servings" @update:scaling-factor="updateScalingFactor" />
                        </div>

                        <!-- Ingredients -->
                        <div class="mt-8">
                            <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">Ingredients</h2>
                            <ul class="space-y-2">
                                <li
                                    v-for="ingredient in recipe.ingredients"
                                    :key="ingredient.id"
                                    class="flex items-center text-gray-700 dark:text-gray-300"
                                >
                                    <div class="mr-3 h-1.5 w-1.5 rounded-full bg-gray-600 dark:bg-gray-400" />
                                    <span class="font-medium">
                                        {{ formatScaledAmount(ingredient.pivot.amount) }}
                                        <template v-if="ingredient.pivot.unit">{{ ingredient.pivot.unit }}</template>
                                    </span>
                                    <span class="ml-1">{{ ingredient.name }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Instructions -->
                        <div class="mt-8">
                            <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">Instructions</h2>
                            <ul class="list-none space-y-6">
                                <li
                                    v-for="(step, index) in parseInstructions(recipe.instructions)"
                                    :key="index"
                                    class="text-gray-700 dark:text-gray-300"
                                >
                                    <h3 class="mb-2 text-lg font-medium text-gray-900 dark:text-white">Step {{ index + 1 }}</h3>
                                    <p>{{ step }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Image carousel column -->
                    <div v-if="recipe.images && recipe.images.length > 0" class="h-80 md:w-1/3">
                        <Carousel :images="recipe.images" :autoplay="true" :interval="5000" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import NutritionInformation from '@/components/Recipe/NutritionInformation.vue';
import ScalingControl from '@/components/Recipe/ScalingControl.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import Carousel from '@/components/ui/carousel.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Recipe } from '@/types/recipe';
import { Head, Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { HeartIcon, PencilIcon } from 'lucide-vue-next';
import { ref } from 'vue';

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

const props = defineProps<{
    recipe: Recipe & { is_favorited?: boolean };
}>();

const isFavorited = ref(props.recipe.is_favorited || false);

const parseInstructions = (instructions: string): string[] => {
    return instructions.split('\n').filter((line) => line.trim());
};

// Scaling functionality
const scalingFactor = ref(1.0);

const updateScalingFactor = (factor: number) => {
    scalingFactor.value = factor;
};

const formatScaledAmount = (amount: number): string => {
    if (!amount) return '0';

    const scaled = amount * scalingFactor.value;

    // For small values, show more decimal places
    if (scaled < 0.1) {
        return scaled.toFixed(2);
    }

    // For values less than 1, show one decimal place
    if (scaled < 1) {
        return scaled.toFixed(1);
    }

    // For values with decimal parts, show one decimal place
    if (scaled % 1 !== 0) {
        return scaled.toFixed(1);
    }

    // For whole numbers, show no decimal places
    return scaled.toFixed(0);
};

const toggleFavorite = () => {
    // Use axios with the CSRF token that Laravel automatically includes
    // when using the default Laravel mix/vite setup
    axios
        .post(route('recipes.favorite', props.recipe.slug))
        .then((response: { data: { favorited: boolean } }) => {
            // The controller returns a JSON response with a 'favorited' boolean
            isFavorited.value = response.data.favorited;
        })
        .catch((error: any) => {
            console.error('Failed to toggle favorite:', error);
        });
};
</script>
