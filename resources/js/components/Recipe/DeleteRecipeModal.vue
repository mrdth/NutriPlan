<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogTrigger asChild>
            <slot>
                <Button variant="destructive">Delete Recipe</Button>
            </slot>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Delete Recipe</DialogTitle>
                <DialogDescription> Are you sure you want to delete this recipe? This action cannot be undone. </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="$emit('update:open', false)">Cancel</Button>
                <Button variant="destructive" @click="deleteRecipe">Delete</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    recipeSlug: string;
    open: boolean;
}>();

defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

function deleteRecipe() {
    router.delete(route('recipes.destroy', { recipe: props.recipeSlug }));
}
</script>
