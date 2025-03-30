import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import HeadingSmall from '../HeadingSmall.vue';

describe('HeadingSmall.vue', () => {
    it('renders the title correctly', () => {
        const title = 'Test Title';
        const wrapper = mount(HeadingSmall, {
            props: {
                title,
            },
        });

        expect(wrapper.find('h3').exists()).toBe(true);
        expect(wrapper.find('h3').text()).toBe(title);
        expect(wrapper.find('h3').classes()).toContain('text-base');
        expect(wrapper.find('h3').classes()).toContain('font-medium');
        expect(wrapper.find('h3').classes()).toContain('mb-0.5');
    });

    it('does not render description when not provided', () => {
        const wrapper = mount(HeadingSmall, {
            props: {
                title: 'Test Title',
            },
        });

        expect(wrapper.find('p').exists()).toBe(false);
    });

    it('renders description when provided', () => {
        const title = 'Test Title';
        const description = 'Test Description';
        const wrapper = mount(HeadingSmall, {
            props: {
                title,
                description,
            },
        });

        expect(wrapper.find('p').exists()).toBe(true);
        expect(wrapper.find('p').text()).toBe(description);
        expect(wrapper.find('p').classes()).toContain('text-sm');
        expect(wrapper.find('p').classes()).toContain('text-muted-foreground');
    });

    it('renders inside header tag', () => {
        const wrapper = mount(HeadingSmall, {
            props: {
                title: 'Test Title',
            },
        });

        expect(wrapper.find('header').exists()).toBe(true);
        expect(wrapper.findAll('header > *').length).toBe(1); // Only h3, no p
    });
});
