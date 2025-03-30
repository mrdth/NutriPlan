import { router } from '@inertiajs/vue3';
import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import DeleteRecipeModal from '../DeleteRecipeModal.vue';

// Mock the Inertia router
vi.mock('@inertiajs/vue3', () => ({
    router: {
        delete: vi.fn(),
    },
}));

// Mock the Dialog components similar to ImportRecipeModal.test.js
vi.mock('@/components/ui/dialog', () => ({
    Dialog: {
        name: 'Dialog',
        template: '<div class="dialog" v-if="open"><slot /><slot name="trigger" /><slot name="content" /></div>',
        props: ['open'],
        emits: ['update:open'],
    },
    DialogContent: {
        name: 'DialogContent',
        template: '<div class="dialog-content"><slot /></div>',
        props: ['class'],
    },
    DialogHeader: {
        name: 'DialogHeader',
        template: '<div class="dialog-header"><slot /></div>',
    },
    DialogTitle: {
        name: 'DialogTitle',
        template: '<h2 class="dialog-title">Delete Recipe</h2>',
    },
    DialogDescription: {
        name: 'DialogDescription',
        template: '<p class="dialog-description">Are you sure you want to delete this recipe? This action cannot be undone.</p>',
    },
    DialogFooter: {
        name: 'DialogFooter',
        template: '<div class="dialog-footer"><slot /></div>',
    },
    DialogTrigger: {
        name: 'DialogTrigger',
        template: '<div class="dialog-trigger"><slot /></div>',
        props: ['asChild'],
    },
}));

// Mock the Button component
vi.mock('@/components/ui/button', () => ({
    Button: {
        name: 'Button',
        template: '<button class="button" :class="variant" @click="$emit(\'click\')"><slot /></button>',
        props: ['variant'],
        emits: ['click'],
    },
}));

describe('DeleteRecipeModal.vue', () => {
    beforeEach(() => {
        vi.clearAllMocks();

        // Mock the route function globally
        global.route = vi.fn().mockImplementation((name, params) => {
            if (name === 'recipes.destroy') {
                return `/recipes/${params.recipe}`;
            }
            return '';
        });
    });

    it('renders properly', async () => {
        const wrapper = mount(DeleteRecipeModal, {
            props: {
                recipeSlug: 'test-recipe',
                open: true,
            },
            global: {
                mocks: {
                    route,
                },
            },
        });

        expect(wrapper.find('.dialog').exists()).toBe(true);
        expect(wrapper.text()).toContain('Delete Recipe');
        expect(wrapper.text()).toContain('cannot be undone');
    });

    it('emits update:open event when cancel is clicked', async () => {
        const wrapper = mount(DeleteRecipeModal, {
            props: {
                recipeSlug: 'test-recipe',
                open: true,
            },
            global: {
                mocks: {
                    route,
                },
            },
        });

        // Find the cancel button (first button with "Cancel" text)
        const cancelButton = wrapper.findAll('button').find(btn => btn.text().includes('Cancel'));
        await cancelButton.trigger('click');

        expect(wrapper.emitted()).toHaveProperty('update:open');
        expect(wrapper.emitted()['update:open'][0]).toEqual([false]);
    });

    it('calls router.delete when delete function is called', async () => {
        // Create the component
        const wrapper = mount(DeleteRecipeModal, {
            props: {
                recipeSlug: 'test-recipe',
                open: true,
            },
            global: {
                mocks: {
                    route,
                },
            },
        });

        // Call the deleteRecipe method directly
        wrapper.vm.deleteRecipe();

        // Check that router.delete was called with the right arguments
        expect(router.delete).toHaveBeenCalledWith('/recipes/test-recipe');
    });
});
