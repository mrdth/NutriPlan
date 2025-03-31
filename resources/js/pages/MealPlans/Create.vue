<template>
    <AppLayout>
        <Head title="Create Meal Plan | NutriPlan" />

        <div class="mx-auto max-w-2xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Create Meal Plan</h1>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Set up a new meal plan for your week or fortnight.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-4">
                    <div>
                        <Label for="name">Name (Optional)</Label>
                        <Input id="name" v-model="form.name" placeholder="e.g., Weekly Family Plan" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div>
                        <Label for="start_date">Start Date</Label>
                        <Input id="start_date" type="date" v-model="form.start_date" required />
                        <InputError :message="form.errors.start_date" />
                    </div>

                    <div>
                        <Label for="duration">Duration</Label>
                        <div class="flex space-x-4">
                            <div class="flex items-center">
                                <input
                                    id="duration-7"
                                    type="radio"
                                    v-model="form.duration"
                                    value="7"
                                    class="text-primary-600 focus:ring-primary-600 h-4 w-4 cursor-pointer border-gray-300"
                                    checked
                                />
                                <label for="duration-7" class="ml-2 block text-sm text-gray-900 dark:text-white"> 7 days (1 week) </label>
                            </div>
                            <div class="flex items-center">
                                <input
                                    id="duration-14"
                                    type="radio"
                                    v-model="form.duration"
                                    value="14"
                                    class="text-primary-600 focus:ring-primary-600 h-4 w-4 cursor-pointer border-gray-300"
                                />
                                <label for="duration-14" class="ml-2 block text-sm text-gray-900 dark:text-white"> 14 days (2 weeks) </label>
                            </div>
                        </div>
                        <InputError :message="form.errors.duration" />
                    </div>

                    <div>
                        <Label for="people_count">Number of People</Label>
                        <div class="flex items-center space-x-2">
                            <Button type="button" variant="outline" size="icon" @click="decrementPeople" :disabled="form.people_count <= 1">
                                <MinusIcon class="h-4 w-4" />
                            </Button>
                            <Input id="people_count" type="number" v-model="form.people_count" class="w-20 text-center" min="1" max="20" required />
                            <Button type="button" variant="outline" size="icon" @click="incrementPeople" :disabled="form.people_count >= 20">
                                <PlusIcon class="h-4 w-4" />
                            </Button>
                        </div>
                        <InputError :message="form.errors.people_count" />
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <Button type="button" variant="outline" @click="cancel">Cancel</Button>
                    <Button type="submit" :disabled="form.processing">Create Meal Plan</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { InputError } from '@/components/ui/input-error';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { MinusIcon, PlusIcon } from 'lucide-vue-next';

const form = useForm({
    name: '',
    start_date: new Date().toISOString().slice(0, 10), // Today's date in YYYY-MM-DD format
    duration: '7',
    people_count: 2,
});

function incrementPeople() {
    if (form.people_count < 20) {
        form.people_count++;
    }
}

function decrementPeople() {
    if (form.people_count > 1) {
        form.people_count--;
    }
}

function submit() {
    form.post(route('meal-plans.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
}

function cancel() {
    window.history.back();
}
</script>
