import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import TextLink from '../TextLink.vue';

// Mock Inertia Link component
vi.mock('@inertiajs/vue3', () => ({
    Link: {
        template: '<a :href="href" :tabindex="tabindex" :method="method" :as="as" :class="$attrs.class"><slot /></a>',
        props: ['href', 'tabindex', 'method', 'as'],
    },
}));

describe('TextLink.vue', () => {
    it('renders the link with default props', () => {
        const wrapper = mount(TextLink, {
            props: {
                href: '/test-route',
            },
            slots: {
                default: 'Link Text',
            },
        });

        expect(wrapper.html()).toContain('href="/test-route"');
        expect(wrapper.text()).toBe('Link Text');
        expect(wrapper.classes()).toContain('text-foreground');
        expect(wrapper.classes()).toContain('underline');
    });

    it('passes tabindex prop correctly', () => {
        const wrapper = mount(TextLink, {
            props: {
                href: '/test-route',
                tabindex: 2,
            },
        });

        expect(wrapper.attributes('tabindex')).toBe('2');
    });

    it('passes method prop correctly', () => {
        const wrapper = mount(TextLink, {
            props: {
                href: '/test-route',
                method: 'post',
            },
        });

        expect(wrapper.attributes('method')).toBe('post');
    });

    it('passes as prop correctly', () => {
        const wrapper = mount(TextLink, {
            props: {
                href: '/test-route',
                as: 'button',
            },
        });

        expect(wrapper.attributes('as')).toBe('button');
    });

    it('renders slot content correctly', () => {
        const wrapper = mount(TextLink, {
            props: {
                href: '/test-route',
            },
            slots: {
                default: '<span class="test-span">Custom Content</span>',
            },
        });

        expect(wrapper.find('.test-span').exists()).toBe(true);
        expect(wrapper.find('.test-span').text()).toBe('Custom Content');
    });
});
