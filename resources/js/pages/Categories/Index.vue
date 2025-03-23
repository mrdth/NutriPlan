<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

interface Category {
    id: number;
    name: string;
    slug: string;
    recipes_count: number;
}

interface Props {
    categories: Category[];
}

const props = defineProps<Props>();

// Calculate font size based on recipe count
const getFontSize = (count: number): number => {
    const min = Math.min(...props.categories.map((c) => c.recipes_count));
    const max = Math.max(...props.categories.map((c) => c.recipes_count));

    // If all categories have the same count, return a default size
    if (min === max) return 1.2;

    // Scale between 0.8 and 2.5 based on recipe count
    const scale = (count - min) / (max - min);
    return 0.8 + scale * 1.7;
};

// Get color opacity based on recipe count
const getOpacity = (count: number): number => {
    const min = Math.min(...props.categories.map((c) => c.recipes_count));
    const max = Math.max(...props.categories.map((c) => c.recipes_count));

    // If all categories have the same count, return a default opacity
    if (min === max) return 0.8;

    // Scale between 0.5 and 1 based on recipe count
    const scale = (count - min) / (max - min);
    return 0.5 + scale * 0.5;
};

// Shuffle the categories to make the cloud more visually interesting
const shuffledCategories = computed(() => {
    return [...props.categories].sort(() => Math.random() - 0.5);
});

// Container for the tag cloud
const cloudContainer = ref<HTMLElement | null>(null);

// Adjust positions to avoid overlaps as much as possible
onMounted(() => {
    if (!cloudContainer.value) return;

    // Give the browser time to render the initial layout
    setTimeout(() => {
        const elements = cloudContainer.value?.querySelectorAll('.category-tag') || [];
        if (elements.length === 0) return;

        // Apply random rotations for visual interest
        elements.forEach((el) => {
            const rotation = Math.random() * 10 - 5; // Random rotation between -5 and 5 degrees
            (el as HTMLElement).style.transform = `rotate(${rotation}deg)`;
        });
    }, 100);
});
</script>

<template>
    <AppLayout>
        <Head title="Categories" />

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">Categories</h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">
                        Browse recipes by category. The size of each category represents how many recipes use it.
                    </p>
                </div>
            </div>

            <div v-if="categories.length === 0" class="mt-16 text-center">
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No categories</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Categories will appear here once recipes have been tagged with them.</p>
            </div>

            <div v-else class="mt-12">
                <div ref="cloudContainer" class="flex flex-wrap justify-center gap-4 py-8">
                    <Link
                        v-for="category in shuffledCategories"
                        :key="category.id"
                        :href="route('categories.show', category.slug)"
                        class="category-tag transition-all duration-300 hover:scale-110 hover:shadow-md"
                        :style="{
                            fontSize: `${getFontSize(category.recipes_count)}rem`,
                            opacity: getOpacity(category.recipes_count),
                        }"
                    >
                        <span class="inline-block rounded-full bg-gray-100 px-4 py-2 font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                            {{ category.name }}
                            <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">({{ category.recipes_count }})</span>
                        </span>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.category-tag {
    display: inline-block;
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;
}

.category-tag:hover {
    z-index: 10;
}
</style>
