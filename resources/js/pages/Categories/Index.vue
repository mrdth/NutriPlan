<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

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

// Filter input and debounce handling
const filterText = ref('');
const debouncedFilterText = ref('');
const isFiltering = ref(false);

// Debounce the filter input to prevent too many updates
let debounceTimeout: number | null = null;
watch(filterText, (newValue) => {
    isFiltering.value = true;
    if (debounceTimeout) {
        clearTimeout(debounceTimeout);
    }

    debounceTimeout = window.setTimeout(() => {
        debouncedFilterText.value = newValue;
        isFiltering.value = false;
    }, 300); // 300ms debounce delay
});

// Filter categories based on the debounced input
const filteredCategories = computed(() => {
    if (!debouncedFilterText.value) return props.categories;

    return props.categories.filter((category) => {
        // Case-insensitive fuzzy matching
        const searchTerm = debouncedFilterText.value.toLowerCase();
        const categoryName = category.name.toLowerCase();

        // Simple fuzzy search - check if all characters in the search term
        // appear in the category name in the correct order
        let idx = 0;
        for (const char of searchTerm) {
            idx = categoryName.indexOf(char, idx);
            if (idx === -1) return false;
            idx += 1;
        }

        return true;
    });
});

// Shuffle the categories to make the cloud more visually interesting
const shuffledCategories = computed(() => {
    return [...filteredCategories.value].sort(() => Math.random() - 0.5);
});

// Container for the tag cloud
const cloudContainer = ref<HTMLElement | null>(null);

const randomizeCloudElementPositions = () => {
    const elements = cloudContainer.value?.querySelectorAll('.category-tag') || [];
    if (elements.length === 0) return;

    // Apply random rotations for visual interest
    elements.forEach((el) => {
        const rotation = Math.random() * 10 - 5; // Random rotation between -5 and 5 degrees
        (el as HTMLElement).style.transform = `rotate(${rotation}deg)`;
    });
};

// Adjust positions to avoid overlaps as much as possible
onMounted(() => {
    if (!cloudContainer.value) return;

    // Give the browser time to render the initial layout
    setTimeout(() => {
        randomizeCloudElementPositions();
    }, 100);
});

// Watch for changes in filtered categories and re-randomize positions
watch(
    () => filteredCategories.value,
    () => {
        // Wait for the DOM to update with the new filtered categories
        setTimeout(() => {
            randomizeCloudElementPositions();
        }, 100);
    },
    { deep: true },
);
</script>

<template>
    <AppLayout>
        <Head title="Categories" />

        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">Categories</h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">
                        Browse recipes by category. The size of each category represents how many recipes use it.
                    </p>
                </div>
            </div>

            <!-- Filter input -->
            <div class="mx-auto mt-4">
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg
                            class="h-5 w-5 text-gray-400"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <input
                        type="text"
                        v-model="filterText"
                        class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:placeholder:text-gray-500 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                        placeholder="Filter categories..."
                        aria-label="Filter categories"
                    />
                </div>
            </div>

            <!-- Fixed height container for all states -->
            <div class="mt-12 flex min-h-[400px] items-center justify-center">
                <!-- No categories state -->
                <div v-if="filteredCategories.length === 0 && !isFiltering" class="w-full text-center">
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No categories</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Categories will appear here once recipes have been tagged with them.</p>
                </div>

                <!-- Loading state -->
                <div v-else-if="isFiltering" class="w-full text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Filtering categories...</p>
                </div>

                <!-- No results state -->
                <div v-else-if="filteredCategories.length === 0 && debouncedFilterText" class="w-full text-center">
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No matching categories</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try a different filter term.</p>
                </div>

                <!-- Categories cloud -->
                <div v-else class="w-full">
                    <div ref="cloudContainer" class="container mx-auto flex flex-wrap justify-center gap-4 py-8">
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
                            <span
                                class="inline-block rounded-full bg-gray-100 px-4 py-2 font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-200"
                            >
                                {{ category.name }}
                                <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">({{ category.recipes_count }})</span>
                            </span>
                        </Link>
                    </div>
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
