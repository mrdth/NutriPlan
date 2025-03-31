<template>
    <AppLayout>
        <Head :title="`${mealPlan.name || 'Meal Plan'} | NutriPlan`" />

        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ mealPlan.name || `Meal Plan (${formatDate(mealPlan.start_date)})` }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">
                        {{ formatDate(mealPlan.start_date) }} to {{ formatDate(mealPlan.end_date) }} • {{ mealPlan.people_count }} people
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <Button variant="destructive" size="sm" @click="confirmDeleteMealPlan">
                        <TrashIcon class="mr-2 h-4 w-4" />
                        Delete
                    </Button>
                </div>
            </div>

            <div class="rounded-lg border dark:border-gray-800">
                <div class="p-6">
                    <p class="text-gray-700 dark:text-gray-300">
                        This meal plan is currently empty. In future phases, you'll be able to add recipes and organize them by day.
                    </p>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Dialog :open="isDeleteModalOpen" @update:open="isDeleteModalOpen = $event">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Meal Plan</DialogTitle>
                    <DialogDescription> Are you sure you want to delete this meal plan? This action cannot be undone. </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="isDeleteModalOpen = false">Cancel</Button>
                    <Button type="button" variant="destructive" @click="deleteMealPlan">Delete</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { TrashIcon } from 'lucide-vue-next';
import { ref } from 'vue';

interface MealPlan {
    id: number;
    name: string | null;
    start_date: string;
    end_date: string;
    duration: number;
    people_count: number;
}

const props = defineProps<{
    mealPlan: MealPlan;
}>();

const isDeleteModalOpen = ref(false);
const form = useForm({});

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const confirmDeleteMealPlan = () => {
    isDeleteModalOpen.value = true;
};

const deleteMealPlan = () => {
    form.delete(route('meal-plans.destroy', props.mealPlan.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};
</script>
