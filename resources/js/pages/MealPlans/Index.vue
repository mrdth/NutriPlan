<template>
    <AppLayout>
        <Head title="Meal Plans | NutriPlan" />

        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">Meal Plans</h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Create and manage your meal plans for easy meal preparation.</p>
                </div>
                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <Button as-child>
                        <Link :href="route('meal-plans.create')">
                            <PlusIcon class="mr-2 h-4 w-4" />
                            New Meal Plan
                        </Link>
                    </Button>
                </div>
            </div>

            <div v-if="mealPlans.length === 0" class="mt-8 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <CalendarIcon class="h-12 w-12" />
                </div>
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No meal plans</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new meal plan.</p>
                <div class="mt-6">
                    <Button as-child>
                        <Link :href="route('meal-plans.create')">
                            <PlusIcon class="mr-2 h-4 w-4" />
                            New Meal Plan
                        </Link>
                    </Button>
                </div>
            </div>

            <div v-else class="mt-8 flow-root">
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-800">
                    <li v-for="mealPlan in mealPlans" :key="mealPlan.id" class="py-4" :class="{ 'opacity-60': isPastMealPlan(mealPlan) }">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900">
                                    <CalendarIcon class="h-6 w-6 text-green-600 dark:text-green-300" />
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <Link :href="route('meal-plans.show', mealPlan.id)" class="focus:outline-none">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ mealPlan.name || `Meal Plan (${formatStartDate(mealPlan.start_date)})` }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ formatStartDate(mealPlan.start_date) }} to {{ formatEndDate(mealPlan.start_date, mealPlan.duration) }}
                                    </p>
                                </Link>
                            </div>
                            <div class="flex-shrink-0">
                                <Badge>{{ mealPlan.people_count }} people</Badge>
                            </div>
                            <div class="flex-shrink-0">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as="div">
                                        <Button variant="ghost" size="icon">
                                            <EllipsisVerticalIcon class="h-5 w-5" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem asChild>
                                            <Link :href="route('meal-plans.show', mealPlan.id)">
                                                <EyeIcon class="mr-2 h-4 w-4" />
                                                View
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="confirmDeleteMealPlan(mealPlan)">
                                            <TrashIcon class="mr-2 h-4 w-4" />
                                            Delete
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                        </div>
                    </li>
                </ul>
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
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { CalendarIcon, EllipsisVerticalIcon, EyeIcon, PlusIcon, TrashIcon } from 'lucide-vue-next';
import { ref } from 'vue';

interface MealPlan {
    id: number;
    name: string | null;
    start_date: string;
    end_date: string;
    duration: number;
    people_count: number;
}

defineProps<{
    mealPlans: MealPlan[];
}>();

const isDeleteModalOpen = ref(false);
const mealPlanToDelete = ref<MealPlan | null>(null);

const form = useForm({});

const formatStartDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatEndDate = (dateString: string, numDays: number) => {
    const date = new Date(dateString);
    date.setDate(date.getDate() + numDays);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const calculateEndDate = (dateString: string, numDays: number): Date => {
    const date = new Date(dateString);
    date.setDate(date.getDate() + numDays);
    return date;
};

const isPastMealPlan = (mealPlan: MealPlan): boolean => {
    const endDate = calculateEndDate(mealPlan.start_date, mealPlan.duration);
    const today = new Date();

    // Set today to end of day for more accurate comparison
    today.setHours(0, 0, 0, 0);

    return endDate < today;
};

const confirmDeleteMealPlan = (mealPlan: MealPlan) => {
    mealPlanToDelete.value = mealPlan;
    isDeleteModalOpen.value = true;
};

const deleteMealPlan = () => {
    if (mealPlanToDelete.value) {
        form.delete(route('meal-plans.destroy', mealPlanToDelete.value.id), {
            onSuccess: () => {
                isDeleteModalOpen.value = false;
                mealPlanToDelete.value = null;
            },
        });
    }
};
</script>
