import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import InputError from '../InputError.vue';

describe('InputError.vue', () => {
    it('renders the error message when provided', () => {
        const errorMessage = 'This field is required';
        const wrapper = mount(InputError, {
            props: {
                message: errorMessage,
            },
        });

        expect(wrapper.text()).toBe(errorMessage);
        expect(wrapper.find('p').exists()).toBe(true);
        expect(wrapper.find('p').classes()).toContain('text-red-600');
        expect(wrapper.find('p').classes()).toContain('dark:text-red-500');
    });

    it('has v-show directive when no message is provided', () => {
        const wrapper = mount(InputError);

        // The component should exist but have v-show="false"
        expect(wrapper.find('div').exists()).toBe(true);
        expect(wrapper.find('div').attributes('style')).toContain('display: none');
        expect(wrapper.find('p').exists()).toBe(true);
    });

    it('has v-show directive when message is empty string', () => {
        const wrapper = mount(InputError, {
            props: {
                message: '',
            },
        });

        // The component should exist but have v-show="false"
        expect(wrapper.find('div').exists()).toBe(true);
        expect(wrapper.find('div').attributes('style')).toContain('display: none');
        expect(wrapper.find('p').exists()).toBe(true);
    });

    it('updates v-show directive when the message prop changes', async () => {
        const wrapper = mount(InputError);

        // Initially has v-show="false"
        expect(wrapper.find('div').attributes('style')).toContain('display: none');

        // Update the prop
        await wrapper.setProps({ message: 'New error message' });

        // Now the message should be visible (no display:none style)
        expect(wrapper.find('div').attributes('style')).toBeFalsy();
        expect(wrapper.text()).toBe('New error message');

        // Update to empty message
        await wrapper.setProps({ message: '' });

        // Should have v-show="false" again
        expect(wrapper.find('div').attributes('style')).toContain('display: none');
    });
});
