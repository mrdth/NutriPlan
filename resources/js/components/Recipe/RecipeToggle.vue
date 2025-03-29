<template>
    <div class="flex items-center space-x-2">
        <Toggle v-model:pressed="showMyRecipes" variant="outline" @update:pressed="handleToggleChange">
            {{ showMyRecipes ? 'My Recipes' : 'All Recipes' }}
        </Toggle>
    </div>
</template>

<script setup lang="ts">
import { Toggle } from '@/components/ui/toggle';
import { router } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

interface Props {
    initialShowMyRecipes?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    initialShowMyRecipes: false,
});

const showMyRecipes = ref(props.initialShowMyRecipes);

// Sync with localStorage when changed
watch(showMyRecipes, (value) => {
    localStorage.setItem('my-recipes-show-mine', value ? 'true' : 'false');
});

// Initialize from localStorage on component mount
onMounted(() => {
    const savedPreference = localStorage.getItem('my-recipes-show-mine');
    if (savedPreference !== null) {
        showMyRecipes.value = savedPreference === 'true';

        // If the saved preference doesn't match the current URL state,
        // trigger a navigation to update the view with the preferred filter
        if (showMyRecipes.value !== props.initialShowMyRecipes) {
            handleToggleChange(showMyRecipes.value);
        }
    }
});

const handleToggleChange = (value: boolean) => {
    router.visit(route('recipes.index', { show_mine: value || undefined }), {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    });
};
</script>
