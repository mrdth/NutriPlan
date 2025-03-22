<script setup lang="ts">
import { BookOpen, Clock, Database, Import, Scale, Share2 } from 'lucide-vue-next';

const features = [
    {
        name: 'Recipe Management',
        description: 'Create, organize, and manage your recipes with ease. Add ingredients, instructions, and cooking times all in one place.',
        icon: BookOpen,
    },
    {
        name: 'Meal Planning',
        description: 'Plan your meals for the week ahead. Automatically generate shopping lists based on your meal plan.',
        icon: Clock,
    },
    {
        name: 'Recipe Sharing',
        description: 'Share your favorite recipes with family and friends. Collaborate on meal planning and cooking.',
        icon: Share2,
    },
    {
        name: 'Import Recipes',
        description: 'Import recipes from your favorite cooking websites with our smart recipe scraper.',
        icon: Import,
    },
    {
        name: 'Smart Storage',
        description: 'Your recipes are safely stored and backed up. Access them from any device, anywhere.',
        icon: Database,
    },
    {
        name: 'Scale Recipes',
        description: 'Automatically scale recipe ingredients up or down based on your serving size needs.',
        icon: Scale,
    },
];
</script>

<template>
    <section id="features" class="bg-white py-24 dark:bg-gray-900 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:text-center">
                <h2 class="text-base font-semibold leading-7 text-indigo-600 dark:text-indigo-400">Everything you need</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Powerful features for home cooks</p>
                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    NutriPlan comes packed with all the tools you need to organize your recipes, plan your meals, and make cooking easier.
                </p>
            </div>
            <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                    <div v-for="feature in features" :key="feature.name" class="flex flex-col">
                        <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-white">
                            <component :is="feature.icon" class="h-5 w-5 flex-none text-indigo-600 dark:text-indigo-400" aria-hidden="true" />
                            {{ feature.name }}
                        </dt>
                        <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                            <p class="flex-auto">{{ feature.description }}</p>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>
</template>
