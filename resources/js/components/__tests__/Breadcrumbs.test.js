import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import Breadcrumbs from '../Breadcrumbs.vue';

// Mock the breadcrumb UI components
vi.mock('@/components/ui/breadcrumb', () => ({
    Breadcrumb: {
        template: '<nav aria-label="breadcrumb" class="breadcrumb-nav"><slot /></nav>',
    },
    BreadcrumbList: {
        template: '<ol class="breadcrumb-list"><slot /></ol>',
    },
    BreadcrumbItem: {
        template: '<li class="breadcrumb-item"><slot /></li>',
    },
    BreadcrumbLink: {
        template: '<div class="breadcrumb-link" :as-child="asChild"><slot /></div>',
        props: ['asChild'],
    },
    BreadcrumbPage: {
        template: '<span class="breadcrumb-page" aria-current="page"><slot /></span>',
    },
    BreadcrumbSeparator: {
        template: '<li class="breadcrumb-separator" aria-hidden="true"><slot /></li>',
    },
}));

// Mock Inertia Link component
vi.mock('@inertiajs/vue3', () => ({
    Link: {
        template: '<a :href="href" class="inertia-link"><slot /></a>',
        props: ['href'],
    },
}));

describe('Breadcrumbs.vue', () => {
    it('renders without breadcrumbs', () => {
        const wrapper = mount(Breadcrumbs, {
            props: {
                breadcrumbs: [],
            },
        });

        expect(wrapper.find('.breadcrumb-nav').exists()).toBe(true);
        expect(wrapper.find('.breadcrumb-list').exists()).toBe(true);
        expect(wrapper.findAll('.breadcrumb-item').length).toBe(0);
    });

    it('renders a single breadcrumb as page', () => {
        const breadcrumbs = [{ title: 'Home', href: '/' }];

        const wrapper = mount(Breadcrumbs, {
            props: {
                breadcrumbs,
            },
        });

        expect(wrapper.findAll('.breadcrumb-item').length).toBe(1);
        expect(wrapper.find('.breadcrumb-page').exists()).toBe(true);
        expect(wrapper.find('.breadcrumb-page').text()).toBe('Home');
        expect(wrapper.findAll('.breadcrumb-separator').length).toBe(0);
    });

    it('renders multiple breadcrumbs with separators', () => {
        const breadcrumbs = [
            { title: 'Home', href: '/' },
            { title: 'Blog', href: '/blog' },
            { title: 'Article', href: '/blog/article' },
        ];

        const wrapper = mount(Breadcrumbs, {
            props: {
                breadcrumbs,
            },
        });

        const items = wrapper.findAll('.breadcrumb-item');
        const separators = wrapper.findAll('.breadcrumb-separator');

        expect(items.length).toBe(3);
        expect(separators.length).toBe(2); // Should have n-1 separators

        // Test content
        expect(items[0].text()).toContain('Home');
        expect(items[1].text()).toContain('Blog');
        expect(items[2].text()).toContain('Article');
    });

    it('renders links for all but the last breadcrumb', () => {
        const breadcrumbs = [
            { title: 'Home', href: '/' },
            { title: 'Blog', href: '/blog' },
            { title: 'Article', href: '/blog/article' },
        ];

        const wrapper = mount(Breadcrumbs, {
            props: {
                breadcrumbs,
            },
        });

        const links = wrapper.findAll('.inertia-link');
        expect(links.length).toBe(2); // First two items should have links

        // Verify hrefs
        expect(links[0].attributes('href')).toBe('/');
        expect(links[1].attributes('href')).toBe('/blog');

        // Last item should be a page, not a link
        const page = wrapper.find('.breadcrumb-page');
        expect(page.exists()).toBe(true);
        expect(page.text()).toBe('Article');
    });

    it('uses # as fallback for missing href', () => {
        const breadcrumbs = [
            { title: 'Home' }, // Missing href
            { title: 'Blog', href: '/blog' },
        ];

        const wrapper = mount(Breadcrumbs, {
            props: {
                breadcrumbs,
            },
        });

        const link = wrapper.find('.inertia-link');
        expect(link.attributes('href')).toBe('#');
    });

    it('handles breadcrumb with arbitrary props', () => {
        const breadcrumbs = [
            { title: 'Home', href: '/', extraProp: 'extra' },
            { title: 'Blog', href: '/blog', id: 'blog-crumb' },
        ];

        const wrapper = mount(Breadcrumbs, {
            props: {
                breadcrumbs,
            },
        });

        // Should still work normally despite extra props
        const items = wrapper.findAll('.breadcrumb-item');
        expect(items.length).toBe(2);
        expect(items[0].text()).toContain('Home');
        expect(items[1].text()).toContain('Blog');
    });
});
