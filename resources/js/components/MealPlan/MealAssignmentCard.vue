<template>
    <div :class="[
        'border-t py-2 transition-colors',
        assignment.to_cook ? 'border-amber-400 bg-amber-50 dark:border-amber-600 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-700',
    ]">
        <div class="flex items-center justify-between">
            <div class="flex items-start gap-2">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ assignment.meal_plan_recipe.recipe.title }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatServings(assignment.servings) }}
                        servings</p>
                </div>
            </div>

            <div class="flex flex-col items-center space-y-2">
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="ghost" size="icon" class="h-6 w-6">
                            <EllipsisVerticalIcon class="h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem @click="toggleToCook">
                            <ChefHatIcon class="mr-2 h-4 w-4" />
                            {{ assignment.to_cook ? 'Remove "to cook" flag' : 'Mark as "to cook"' }}
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="$emit('edit', assignment)">
                            <PencilIcon class="mr-2 h-4 w-4" />
                            Edit Servings
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="$emit('remove', assignment)"
                            class="text-red-600 focus:text-red-600 dark:focus:text-red-400">
                            <TrashIcon class="mr-2 h-4 w-4" />
                            Remove
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
                <div class="rounded p-1" :title="assignment.to_cook ? 'Marked to cook' : 'Not marked to cook'">
                    <ChefHatIcon :class="[
                        'h-5 w-5 transition-colors',
                        assignment.to_cook ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400 dark:text-gray-600',
                    ]" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import type { MealAssignment } from '@/types/meal-plan';
import axios from 'axios';
import { ChefHatIcon, EllipsisVerticalIcon, PencilIcon, TrashIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    assignment: MealAssignment;
}>();

const emit = defineEmits<{
    (e: 'edit', assignment: MealAssignment): void;
    (e: 'remove', assignment: MealAssignment): void;
    (e: 'toggled', assignment: MealAssignment): void;
}>();

const isUpdating = ref(false);

const formatServings = (servings: number): string => {
    return servings.toString();
};

const toggleToCook = async () => {
    if (isUpdating.value) return;

    try {
        isUpdating.value = true;
        const response = await axios.post(route('meal-assignments.toggle-cook', props.assignment.id));

        if (response.data.success) {
            emit('toggled', {
                ...props.assignment,
                to_cook: response.data.to_cook,
            });
        }
    } catch (error) {
        console.error('Failed to toggle cooking status:', error);
    } finally {
        isUpdating.value = false;
    }
};
</script>
