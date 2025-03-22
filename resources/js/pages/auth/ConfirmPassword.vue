<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AuthLayout title="Confirm your password" description="This is a secure area of the application. Please confirm your password before continuing.">
        <Head title="Confirm password" />

        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-6">
                <div class="space-y-2">
                    <Label htmlFor="password" class="block text-sm font-medium text-gray-900 dark:text-white">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        autofocus
                        placeholder="Enter your password"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:placeholder:text-gray-500 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <Button
                    type="submit"
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400"
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                    Confirm Password
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
