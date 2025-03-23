<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative isolate min-h-svh overflow-hidden bg-white dark:bg-gray-900">
        <!-- Background pattern -->
        <div class="absolute inset-0 z-0 opacity-30 dark:opacity-20">
            <div
                class="absolute inset-y-0 right-1/2 -z-10 mr-16 w-[200%] origin-bottom-left skew-x-[-30deg] bg-white/80 shadow-xl shadow-indigo-600/10 ring-1 ring-indigo-50 dark:bg-gray-800/80 dark:ring-gray-700 sm:mr-28 lg:mr-0 xl:mr-16 xl:origin-center"
            ></div>
        </div>

        <div class="relative z-10 mx-auto flex min-h-svh max-w-7xl flex-col items-center justify-center px-6 py-12 lg:px-8">
            <div class="w-full max-w-sm space-y-8">
                <div class="flex flex-col items-center gap-4">
                    <Link :href="route('home')" class="flex flex-col items-center gap-2">
                        <div class="mb-1 flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 dark:bg-indigo-500">
                            <AppLogoIcon class="size-12 fill-current text-white" />
                        </div>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ title }}</h1>
                        <p class="text-center text-sm text-gray-600 dark:text-gray-300">{{ description }}</p>
                    </div>
                </div>

                <div
                    class="mt-10 rounded-xl bg-white/70 p-6 shadow-sm ring-1 ring-gray-900/5 backdrop-blur-sm dark:bg-gray-800/70 dark:ring-white/10"
                >
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
