<template>
    <div class="flex flex-col space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Adjust servings</span>
            <div v-if="isScaled" class="flex items-center">
                <span class="text-xs text-amber-600 dark:text-amber-400">Recipe scaled</span>
                <button @click="resetToOriginal" class="ml-2 text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    Reset
                </button>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button
                @click="decreaseServings"
                class="flex h-8 w-8 items-center justify-center rounded-md border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                :disabled="servings <= 0.5"
                :data-disabled="servings <= 0.5"
            >
                <MinusIcon class="h-4 w-4" />
            </button>
            <div class="relative flex items-center">
                <input
                    v-model.number="servings"
                    type="number"
                    min="0.5"
                    step="0.5"
                    class="focus:border-primary-500 focus:ring-primary-500 h-8 w-16 rounded-md border border-gray-300 px-3 py-1 text-center text-sm shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                    @change="updateServings"
                />
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">servings</span>
            </div>
            <button
                @click="increaseServings"
                class="flex h-8 w-8 items-center justify-center rounded-md border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
            >
                <PlusIcon class="h-4 w-4" />
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { MinusIcon, PlusIcon } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Props {
    originalServings: number;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'update:scalingFactor', value: number): void;
}>();

const originalServings = ref(props.originalServings);
const servings = ref(props.originalServings);

// Compute the scaling factor based on the current servings vs original servings
const scalingFactor = computed(() => {
    return servings.value / originalServings.value;
});

// Determine if the recipe has been scaled from its original servings
const isScaled = computed(() => {
    return servings.value !== originalServings.value;
});

// Watch for changes to the scaling factor and emit the updated value
watch(scalingFactor, (newFactor) => {
    emit('update:scalingFactor', newFactor);
});

// Increase servings by 0.5
const increaseServings = () => {
    servings.value += 0.5;
    updateServings();
};

// Decrease servings by 0.5, but not below 0.5
const decreaseServings = () => {
    if (servings.value > 0.5) {
        servings.value -= 0.5;
        updateServings();
    }
};

// Reset to the original recipe servings
const resetToOriginal = () => {
    servings.value = originalServings.value;
    updateServings();
};

// Update servings and ensure it's at least 0.5
const updateServings = () => {
    // Ensure servings is a valid number
    const numValue = Number(servings.value);

    // If invalid, reset to original servings
    if (isNaN(numValue)) {
        servings.value = originalServings.value;
        return;
    }

    // Ensure servings is at least 0.5
    if (numValue < 0.5) {
        servings.value = 0.5;
    }
};
</script>
