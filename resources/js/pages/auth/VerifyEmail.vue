<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <AuthLayout title="Verify email" description="Please verify your email address by clicking on the link we just emailed to you.">
        <Head title="Email verification" />

        <div v-if="status === 'verification-link-sent'" class="mb-6 rounded-md bg-green-50 p-4 dark:bg-green-900/50">
            <p class="text-center text-sm font-medium text-green-800 dark:text-green-200">
                A new verification link has been sent to the email address you provided during registration.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6 text-center">
            <Button
                :disabled="form.processing"
                class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:hover:bg-gray-700/50"
            >
                <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                Resend verification email
            </Button>

            <TextLink
                :href="route('logout')"
                method="post"
                as="button"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
                Log out
            </TextLink>
        </form>
    </AuthLayout>
</template>
