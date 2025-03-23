<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ClockIcon, UsersIcon } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    recipe: {
        id: number;
        title: string;
        description: string | null;
        slug: string;
        prep_time: number;
        cooking_time: number;
        servings: number;
        images: string[];
        url: string | null;
        user: {
            name: string;
        };
        categories: {
            id: number;
            name: string;
            recipe_count: number;
        }[];
    };
}

const props = defineProps<Props>();

const formatTime = (minutes: number): string => {
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;

    if (hours === 0) {
        return `${remainingMinutes}m`;
    }

    return remainingMinutes === 0 ? `${hours}h` : `${hours}h ${remainingMinutes}m`;
};

const topCategories = computed(() => {
    return [...props.recipe.categories].sort((a, b) => b.recipe_count - a.recipe_count).slice(0, 3);
});

const sitename = computed(() => {
    if (!props.recipe.url) return null;
    try {
        const url = new URL(props.recipe.url);
        return url.hostname.replace(/^www\./, '');
    } catch {
        return null;
    }
});
</script>

<template>
    <article class="group relative flex flex-col overflow-hidden rounded-lg border dark:border-gray-800">
        <div class="aspect-h-3 aspect-w-4 bg-gray-200 dark:bg-gray-800">
            <img v-if="recipe.images?.length" :src="recipe.images[0]" :alt="recipe.title" class="h-full w-full object-cover object-center" />
            <img
                v-else
                src="https://placehold.co/600x400?text=No+image+available"
                alt="No image available"
                class="h-full w-full object-cover object-center"
            />
        </div>

        <div class="flex flex-1 flex-col space-y-2 p-4">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                <Link :href="route('recipes.show', recipe.slug)">
                    <span aria-hidden="true" class="absolute inset-0" />
                    {{ recipe.title }}
                </Link>
            </h3>

            <p v-if="recipe.description" class="text-sm text-gray-500 dark:text-gray-400">
                {{ recipe.description }}
            </p>

            <div class="mt-auto flex items-center justify-between text-xs">
                <div class="flex items-center space-x-4 text-gray-600 dark:text-gray-400">
                    <div class="flex items-center space-x-1">
                        <ClockIcon class="h-4 w-4" />
                        <span>{{ formatTime(recipe.prep_time + recipe.cooking_time) }}</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <UsersIcon class="h-4 w-4" />
                        <span>{{ recipe.servings }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-1">
                    <a
                        v-if="recipe.url && sitename"
                        :href="recipe.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 hover:bg-blue-200 dark:bg-blue-800 dark:text-blue-200 dark:hover:bg-blue-700"
                    >
                        {{ sitename }}
                    </a>
                    <span
                        v-for="category in topCategories"
                        :key="category.id"
                        class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-200"
                    >
                        {{ category.name }}
                    </span>
                </div>
            </div>
        </div>
    </article>
</template>
