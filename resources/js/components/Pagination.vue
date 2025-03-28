<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
    links: Array<{
        url?: string;
        label: string;
        active: boolean;
    }>;
}

defineProps<Props>();
</script>

<template>
    <div class="flex items-center justify-center gap-1">
        <Link
            v-for="link in links"
            :key="link.label"
            :href="link.url || '#'"
            class="inline-flex min-w-[2rem] items-center justify-center rounded-md px-3 py-2 text-sm font-medium ring-offset-white transition-colors hover:bg-gray-100 hover:text-gray-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 dark:hover:bg-gray-800 dark:hover:text-gray-50 dark:focus-visible:ring-gray-300"
            :class="{
                'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-50': link.active,
                'text-gray-500 dark:text-gray-400': !link.active,
                'cursor-default': !link.url,
            }"
            >{{ link.label }}</Link
        >
    </div>
</template>
