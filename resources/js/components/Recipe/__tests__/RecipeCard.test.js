import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import RecipeCard from '../RecipeCard.vue';

// Mock the components and dependencies
vi.mock('@/components/ui/button', () => ({
    Button: {
        name: 'Button',
        template: '<button class="button" :type="type" :disabled="disabled"><slot /></button>',
        props: ['type', 'variant', 'size', 'disabled'],
    },
}));

vi.mock('@/components/ui/dialog', () => ({
    Dialog: {
        name: 'Dialog',
        template: '<div class="dialog" v-if="open"><slot /></div>',
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
        template: '<h2 class="dialog-title"><slot /></h2>',
    },
    DialogDescription: {
        name: 'DialogDescription',
        template: '<p class="dialog-description"><slot /></p>',
    },
    DialogFooter: {
        name: 'DialogFooter',
        template: '<div class="dialog-footer"><slot /></div>',
    },
}));

vi.mock('@/components/ui/dropdown-menu', () => ({
    DropdownMenu: {
        name: 'DropdownMenu',
        template: '<div class="dropdown-menu"><slot /></div>',
    },
    DropdownMenuTrigger: {
        name: 'DropdownMenuTrigger',
        template: '<div class="dropdown-menu-trigger"><slot /></div>',
        props: ['as'],
    },
    DropdownMenuContent: {
        name: 'DropdownMenuContent',
        template: '<div class="dropdown-menu-content"><slot /></div>',
        props: ['align'],
    },
    DropdownMenuItem: {
        name: 'DropdownMenuItem',
        template: '<div class="dropdown-menu-item" @click="$emit(\'click\')"><slot /></div>',
    },
}));

vi.mock('@/components/ui/input-error', () => ({
    InputError: {
        name: 'InputError',
        template: '<div class="input-error" v-if="message">{{ message }}</div>',
        props: ['message'],
    },
}));

vi.mock('@/components/ui/label', () => ({
    Label: {
        name: 'Label',
        template: '<label class="label"><slot /></label>',
        props: ['for'],
    },
}));

vi.mock('@inertiajs/vue3', () => ({
    Link: {
        name: 'Link',
        template: '<a :href="href" class="link"><slot /></a>',
        props: ['href'],
    },
    useForm: () => ({
        collection_id: '',
        recipe_id: 1,
        errors: {},
        processing: false,
        post: vi.fn().mockImplementation((route, options) => {
            if (options && options.onSuccess) {
                options.onSuccess();
            }
        }),
    }),
}));

vi.mock('axios', () => ({
    default: {
        post: vi.fn().mockImplementation(() => Promise.resolve({ data: { favorited: true } })),
    },
}));

vi.mock('lucide-vue-next', () => ({
    ClockIcon: {
        name: 'ClockIcon',
        template: '<div class="clock-icon"></div>',
    },
    EllipsisVerticalIcon: {
        name: 'EllipsisVerticalIcon',
        template: '<div class="ellipsis-vertical-icon"></div>',
    },
    FolderPlusIcon: {
        name: 'FolderPlusIcon',
        template: '<div class="folder-plus-icon"></div>',
    },
    HeartIcon: {
        name: 'HeartIcon',
        template: '<div class="heart-icon"></div>',
    },
    UsersIcon: {
        name: 'UsersIcon',
        template: '<div class="users-icon"></div>',
    },
}));

// Mock the route function
global.route = vi.fn((name, params) => {
    if (name === 'recipes.show') {
        return `/recipes/${params}`;
    } else if (name === 'categories.show') {
        return `/categories/${params}`;
    } else if (name === 'collections.index') {
        return '/collections';
    } else if (name === 'collections.add-recipe') {
        return '/collections/add-recipe';
    } else if (name === 'recipes.favorite') {
        return `/recipes/${params}/favorite`;
    }
    return '/';
});

describe('RecipeCard.vue', () => {
    const mockRecipe = {
        id: 1,
        title: 'Test Recipe',
        description: 'A delicious test recipe',
        slug: 'test-recipe',
        prep_time: 15,
        cooking_time: 30,
        servings: 4,
        images: ['https://example.com/image.jpg'],
        url: 'https://example.com/recipe',
        user: {
            name: 'John Doe',
        },
        categories: [
            { id: 1, name: 'Dinner', slug: 'dinner', recipe_count: 10 },
            { id: 2, name: 'Italian', slug: 'italian', recipe_count: 5 },
            { id: 3, name: 'Quick', slug: 'quick', recipe_count: 3 },
        ],
        is_favorited: false,
    };

    it('renders recipe card with correct title and description', () => {
        const wrapper = mount(RecipeCard, {
            props: {
                recipe: mockRecipe,
            },
            global: {
                mocks: {
                    route,
                },
            },
        });

        expect(wrapper.text()).toContain('Test Recipe');
        expect(wrapper.text()).toContain('A delicious test recipe');
    });

    it('formats cooking time correctly', () => {
        const wrapper = mount(RecipeCard, {
            props: {
                recipe: mockRecipe,
            },
            global: {
                mocks: {
                    route,
                },
            },
        });

        // 15 + 30 minutes = 45 minutes = "45m"
        expect(wrapper.text()).toContain('45m');
    });

    it('formats hours and minutes correctly', () => {
        const longCookingRecipe = {
            ...mockRecipe,
            prep_time: 20,
            cooking_time: 100, // 1h 40m
        };

        const wrapper = mount(RecipeCard, {
            props: {
                recipe: longCookingRecipe,
            },
            global: {
                mocks: {
                    route,
                },
            },
        });

        // 20 + 100 minutes = 120 minutes = "2h"
        expect(wrapper.text()).toContain('2h');
    });

    it('displays the correct number of servings', () => {
        const wrapper = mount(RecipeCard, {
            props: {
                recipe: mockRecipe,
            },
            global: {
                mocks: {
                    route,
                },
            },
        });

        expect(wrapper.text()).toContain('4');
    });

    it('displays top 3 categories ordered by recipe count', () => {
        const wrapper = mount(RecipeCard, {
            props: {
                recipe: mockRecipe,
            },
            global: {
                mocks: {
                    route,
                },
            },
        });

        const text = wrapper.text();
        expect(text).toContain('Dinner');
        expect(text).toContain('Italian');
        expect(text).toContain('Quick');

        // Check order: we expect Dinner (10) to come before Italian (5)
        const dinnerIndex = text.indexOf('Dinner');
        const italianIndex = text.indexOf('Italian');
        const quickIndex = text.indexOf('Quick');

        expect(dinnerIndex).toBeLessThan(italianIndex);
        expect(italianIndex).toBeLessThan(quickIndex);
    });

    it('displays website domain from URL', () => {
        const wrapper = mount(RecipeCard, {
            props: {
                recipe: mockRecipe,
            },
            global: {
                mocks: {
                    route,
                },
            },
        });

        expect(wrapper.text()).toContain('example.com');
    });

    it('uses placeholder image when no images are provided', () => {
        const noImageRecipe = {
            ...mockRecipe,
            images: [],
        };

        const wrapper = mount(RecipeCard, {
            props: {
                recipe: noImageRecipe,
            },
            global: {
                mocks: {
                    route,
                },
            },
        });

        const img = wrapper.find('img');
        expect(img.attributes('src')).toContain('placehold.co');
        expect(img.attributes('alt')).toBe('No image available');
    });

    it('shows correct favorite status', () => {
        const favoritedRecipe = {
            ...mockRecipe,
            is_favorited: true,
        };

        const wrapper = mount(RecipeCard, {
            props: {
                recipe: favoritedRecipe,
            },
            global: {
                mocks: {
                    route,
                },
            },
        });

        expect(wrapper.text()).toContain('Unfavorite');
    });
});
