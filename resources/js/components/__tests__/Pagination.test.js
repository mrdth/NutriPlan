import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import Pagination from '../Pagination.vue';

describe('Pagination.vue', () => {
    it('renders pagination links correctly with HTML entities', () => {
        const links = [
            {
                url: null,
                label: '&laquo; Previous',
                active: false,
            },
            {
                url: 'http://example.com/page/1',
                label: '1',
                active: true,
            },
            {
                url: 'http://example.com/page/2',
                label: '2',
                active: false,
            },
            {
                url: 'http://example.com/page/2',
                label: 'Next &raquo;',
                active: false,
            },
        ];

        const wrapper = mount(Pagination, {
            props: {
                links,
            },
            global: {
                stubs: {
                    Link: {
                        template: '<a :href="href" class="link-stub" :class="$attrs.class" v-html="$slots.default"></a>',
                        props: ['href'],
                    },
                },
            },
        });

        // Check that all links are rendered
        const linkElements = wrapper.findAll('.link-stub');
        expect(linkElements.length).toBe(links.length);

        // Test that HTML entities are properly rendered as HTML characters
        // Since v-html correctly renders entities, we should see « instead of &laquo;
        expect(wrapper.html()).toContain('« Previous');
        expect(wrapper.html()).toContain('Next »');
    });

    it('applies correct styling based on active state', () => {
        const links = [
            {
                url: 'http://example.com/page/1',
                label: '1',
                active: true,
            },
            {
                url: 'http://example.com/page/2',
                label: '2',
                active: false,
            },
        ];

        const wrapper = mount(Pagination, {
            props: {
                links,
            },
            global: {
                stubs: {
                    Link: {
                        template: '<a :href="href" class="link-stub" :class="$attrs.class" v-html="$slots.default"></a>',
                        props: ['href'],
                    },
                },
            },
        });

        const linkElements = wrapper.findAll('.link-stub');

        // Active link should have active class
        expect(linkElements[0].classes()).toContain('bg-gray-100');
        expect(linkElements[0].classes()).toContain('text-gray-900');

        // Inactive link should have inactive class
        expect(linkElements[1].classes()).toContain('text-gray-500');
        expect(linkElements[1].classes()).not.toContain('bg-gray-100');
    });

    it('disables links with no URL', () => {
        const links = [
            {
                url: null,
                label: 'Previous',
                active: false,
            },
            {
                url: 'http://example.com/page/1',
                label: '1',
                active: true,
            },
        ];

        const wrapper = mount(Pagination, {
            props: {
                links,
            },
            global: {
                stubs: {
                    Link: {
                        template: '<a :href="href" class="link-stub" :class="$attrs.class" v-html="$slots.default"></a>',
                        props: ['href'],
                    },
                },
            },
        });

        const linkElements = wrapper.findAll('.link-stub');

        // Link with no URL should have cursor-default class and href="#"
        expect(linkElements[0].classes()).toContain('cursor-default');
        expect(linkElements[0].attributes('href')).toBe('#');
    });
});
