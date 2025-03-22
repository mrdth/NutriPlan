<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout title="Forgot password" description="Enter your email to receive a password reset link">
        <Head title="Forgot password" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-2">
                <Label for="email" class="block text-sm font-medium text-gray-900 dark:text-white">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="off"
                    v-model="form.email"
                    autofocus
                    placeholder="email@example.com"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:placeholder:text-gray-500 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                />
                <InputError :message="form.errors.email" />
            </div>

            <Button
                type="submit"
                class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400"
                :disabled="form.processing"
            >
                <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                Email password reset link
            </Button>

            <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                Or, return to
                <TextLink
                    :href="route('login')"
                    class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                    log in
                </TextLink>
            </div>
        </form>
    </AuthLayout>
</template>
