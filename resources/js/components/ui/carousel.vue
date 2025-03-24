<template>
    <div class="relative w-full h-full overflow-hidden rounded-lg">
        <!-- Images -->
        <div class="relative h-full">
            <div 
                v-for="(image, index) in images" 
                :key="index"
                class="absolute w-full h-full transition-opacity duration-500 ease-in-out"
                :class="{ 'opacity-100': currentIndex === index, 'opacity-0': currentIndex !== index }"
            >
                <img 
                    :src="image" 
                    :alt="`Image ${index + 1}`"
                    class="w-full h-full object-cover rounded-lg"
                />
            </div>
        </div>

        <!-- Navigation arrows - only show if more than one image -->
        <template v-if="images.length > 1">
            <button 
                @click="prev" 
                class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full"
                aria-label="Previous image"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
            <button 
                @click="next" 
                class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full"
                aria-label="Next image"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Indicators -->
            <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex space-x-2">
                <button 
                    v-for="(_, index) in images" 
                    :key="index"
                    @click="currentIndex = index"
                    class="w-2 h-2 rounded-full transition-colors duration-300"
                    :class="currentIndex === index ? 'bg-white' : 'bg-white/50'"
                    :aria-label="`Go to image ${index + 1}`"
                />
            </div>
        </template>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';

const props = defineProps<{
    images: string[];
    autoplay?: boolean;
    interval?: number;
}>();

const currentIndex = ref(0);
let timer: number | null = null;

const next = () => {
    currentIndex.value = (currentIndex.value + 1) % props.images.length;
};

const prev = () => {
    currentIndex.value = (currentIndex.value - 1 + props.images.length) % props.images.length;
};

const startAutoplay = () => {
    if (props.autoplay && props.images.length > 1) {
        timer = window.setInterval(() => {
            next();
        }, props.interval || 5000);
    }
};

const stopAutoplay = () => {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
};

onMounted(() => {
    startAutoplay();
});

// Reset autoplay when images change
watch(() => props.images, () => {
    stopAutoplay();
    startAutoplay();
});

// Clean up on component unmount
onMounted(() => {
    return () => {
        stopAutoplay();
    };
});
</script>
