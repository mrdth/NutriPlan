import { mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

// Import the router before mocking it
import { router } from '@inertiajs/vue3';

// Mock Inertia router
vi.mock('@inertiajs/vue3', () => ({
    router: {
        visit: vi.fn(),
    },
}));

// Mock route function
vi.stubGlobal(
    'route',
    vi.fn((name, params) => {
        return `/recipes${params?.show_mine ? '?show_mine=1' : ''}`;
    }),
);

import RecipeToggle from '../RecipeToggle.vue';

describe('RecipeToggle', () => {
    beforeEach(() => {
        // Mock localStorage
        vi.stubGlobal('localStorage', {
            getItem: vi.fn(),
            setItem: vi.fn(),
        });
    });

    afterEach(() => {
        vi.clearAllMocks();
    });

    it('renders in "All Recipes" state by default', () => {
        const wrapper = mount(RecipeToggle);
        expect(wrapper.text()).toContain('All Recipes');
        expect(wrapper.text()).not.toContain('My Recipes');
    });

    it('shows "My Recipes" when toggled on', async () => {
        const wrapper = mount(RecipeToggle);
        const toggleButton = wrapper.find('button');

        await toggleButton.trigger('click');

        expect(wrapper.text()).toContain('My Recipes');
        expect(wrapper.text()).not.toContain('All Recipes');
    });

    it('initializes with the provided state', () => {
        const wrapper = mount(RecipeToggle, {
            props: {
                initialShowMyRecipes: true,
            },
        });

        expect(wrapper.text()).toContain('My Recipes');
    });

    it('navigates with the correct params on toggle', async () => {
        const wrapper = mount(RecipeToggle);
        const toggleButton = wrapper.find('button');

        // Toggle on
        await toggleButton.trigger('click');

        expect(vi.mocked(router.visit)).toHaveBeenCalledWith('/recipes?show_mine=1', {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        });

        // Toggle off
        await toggleButton.trigger('click');

        expect(vi.mocked(router.visit)).toHaveBeenCalledWith('/recipes', {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        });
    });

    it('saves preference to localStorage when toggled', async () => {
        const wrapper = mount(RecipeToggle);
        const toggleButton = wrapper.find('button');

        // Toggle on
        await toggleButton.trigger('click');

        expect(localStorage.setItem).toHaveBeenCalledWith('my-recipes-show-mine', 'true');

        // Toggle off
        await toggleButton.trigger('click');

        expect(localStorage.setItem).toHaveBeenCalledWith('my-recipes-show-mine', 'false');
    });

    it('initializes from localStorage on mount', async () => {
        localStorage.getItem = vi.fn().mockReturnValue('true');

        // Mount with initialShowMyRecipes=false but localStorage has 'true'
        mount(RecipeToggle, {
            props: {
                initialShowMyRecipes: false,
            },
        });

        // Should navigate to update URL if localStorage differs from props
        expect(vi.mocked(router.visit)).toHaveBeenCalledWith('/recipes?show_mine=1', {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        });
    });
});
