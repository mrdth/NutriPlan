<template>
    <AppLayout>
        <Head title="Collections | NutriPlan" />

        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">Collections</h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Organize your recipes into collections for easy access.</p>
                </div>
                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <Button @click="openNewCollectionModal">
                        <PlusIcon class="mr-2 h-4 w-4" />
                        New Collection
                    </Button>
                </div>
            </div>

            <div v-if="collections.length === 0" class="mt-8 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <FolderIcon class="h-12 w-12" />
                </div>
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No collections</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new collection.</p>
                <div class="mt-6">
                    <Button @click="openNewCollectionModal">
                        <PlusIcon class="mr-2 h-4 w-4" />
                        New Collection
                    </Button>
                </div>
            </div>

            <div v-else class="mt-8 flow-root">
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-800">
                    <li v-for="collection in collections" :key="collection.id" class="py-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                                    <FolderIcon class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <Link :href="route('collections.show', collection.slug)" class="focus:outline-none">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ collection.name }}
                                    </p>
                                    <p v-if="collection.description" class="truncate text-sm text-gray-500 dark:text-gray-400">
                                        {{ collection.description }}
                                    </p>
                                </Link>
                            </div>
                            <div class="flex-shrink-0">
                                <Badge>{{ collection.recipes_count }} recipes</Badge>
                            </div>
                            <div class="flex-shrink-0">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as="div">
                                        <Button variant="ghost" size="icon">
                                            <EllipsisVerticalIcon class="h-5 w-5" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem @click="editCollection(collection)">
                                            <PencilIcon class="mr-2 h-4 w-4" />
                                            Edit
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="confirmDeleteCollection(collection)">
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

        <!-- New Collection Modal -->
        <Dialog :open="isNewCollectionModalOpen" @update:open="isNewCollectionModalOpen = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ editingCollection ? 'Edit Collection' : 'New Collection' }}</DialogTitle>
                    <DialogDescription>
                        {{ editingCollection ? 'Update your collection details.' : 'Create a new collection to organize your recipes.' }}
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="saveCollection">
                    <div class="space-y-4 py-4">
                        <div class="space-y-2">
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="form.description" />
                            <InputError :message="form.errors.description" />
                        </div>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="closeNewCollectionModal">Cancel</Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ editingCollection ? 'Update' : 'Create' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Modal -->
        <Dialog :open="isDeleteModalOpen" @update:open="isDeleteModalOpen = $event">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Collection</DialogTitle>
                    <DialogDescription> Are you sure you want to delete this collection? This action cannot be undone. </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="isDeleteModalOpen = false">Cancel</Button>
                    <Button type="button" variant="destructive" @click="deleteCollection">Delete</Button>
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
import { Input } from '@/components/ui/input';
import { InputError } from '@/components/ui/input-error';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EllipsisVerticalIcon, FolderIcon, PencilIcon, PlusIcon, TrashIcon } from 'lucide-vue-next';
import { ref } from 'vue';

interface Collection {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    recipes_count: number;
}

defineProps<{
    collections: Collection[];
}>();

const isNewCollectionModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const editingCollection = ref<Collection | null>(null);
const collectionToDelete = ref<Collection | null>(null);

const form = useForm({
    name: '',
    description: '',
});

const openNewCollectionModal = () => {
    editingCollection.value = null;
    form.name = '';
    form.description = '';
    isNewCollectionModalOpen.value = true;
};

const closeNewCollectionModal = () => {
    isNewCollectionModalOpen.value = false;
    form.clearErrors();
};

const editCollection = (collection: Collection) => {
    editingCollection.value = collection;
    form.name = collection.name;
    form.description = collection.description || '';
    isNewCollectionModalOpen.value = true;
};

const saveCollection = () => {
    if (editingCollection.value) {
        form.put(route('collections.update', editingCollection.value.slug), {
            onSuccess: () => {
                isNewCollectionModalOpen.value = false;
            },
        });
    } else {
        form.post(route('collections.store'), {
            onSuccess: () => {
                isNewCollectionModalOpen.value = false;
            },
        });
    }
};

const confirmDeleteCollection = (collection: Collection) => {
    collectionToDelete.value = collection;
    isDeleteModalOpen.value = true;
};

const deleteCollection = () => {
    if (collectionToDelete.value) {
        form.delete(route('collections.destroy', collectionToDelete.value.slug), {
            onSuccess: () => {
                isDeleteModalOpen.value = false;
                collectionToDelete.value = null;
            },
        });
    }
};
</script>
