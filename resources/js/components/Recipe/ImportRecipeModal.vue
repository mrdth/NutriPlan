<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Import Recipe</DialogTitle>
                <DialogDescription> Enter the URL of a recipe to import it into your collection. </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="submit">
                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label for="url">Recipe URL</Label>
                        <Input id="url" v-model="form.url" type="url" placeholder="https://example.com/recipe"
                            required />
                        <InputError :message="form.errors.url" />
                    </div>
                </div>
                <DialogFooter>
                    <Button type="button" variant="ghost" @click="$emit('update:open', false)"> Cancel </Button>
                    <Button type="submit" :disabled="form.processing">
                        <Loader2Icon v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Import Recipe
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { Loader2Icon } from 'lucide-vue-next';

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

defineProps<{
    open: boolean;
}>();

const form = useForm({
    url: '',
});

const submit = () => {
    form.post(route('recipes.import'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('update:open', false);
        },
    });
};
</script>
