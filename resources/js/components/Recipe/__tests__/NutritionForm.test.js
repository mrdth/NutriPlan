import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import { nextTick } from 'vue';
import NutritionForm from '../NutritionForm.vue';

// Mock the UI components
vi.mock('@/components/ui/button', () => ({
    Button: {
        name: 'Button',
        template: '<button><slot /></button>',
    },
}));

vi.mock('@/components/ui/input', () => ({
    Input: {
        name: 'Input',
        template: '<input :value="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)" />',
        props: ['modelValue'],
    },
}));

vi.mock('@/components/ui/label', () => ({
    Label: {
        name: 'Label',
        template: '<label><slot /></label>',
    },
}));

vi.mock('lucide-vue-next', () => ({
    ChevronDownIcon: {
        name: 'ChevronDownIcon',
        template: '<div>ChevronDownIcon</div>',
    },
    ChevronRightIcon: {
        name: 'ChevronRightIcon',
        template: '<div>ChevronRightIcon</div>',
    },
}));

describe('NutritionForm.vue', () => {
    it('renders collapsed by default', () => {
        const wrapper = mount(NutritionForm, {
            props: {
                modelValue: {},
            },
        });

        expect(wrapper.text()).toContain('Nutrition Information');
        expect(wrapper.text()).toContain('Show');
        expect(wrapper.text()).not.toContain('All fields are optional');
    });

    it('expands when the button is clicked', async () => {
        const wrapper = mount(NutritionForm, {
            props: {
                modelValue: {},
            },
        });

        await wrapper.find('button').trigger('click');

        expect(wrapper.text()).toContain('Hide');
        expect(wrapper.text()).toContain('All fields are optional');
        expect(wrapper.find('input[id="calories"]').exists()).toBe(true);
    });

    it('emits update events when values change', async () => {
        const wrapper = mount(NutritionForm, {
            props: {
                modelValue: {
                    calories: '',
                },
            },
        });

        // Expand the form
        await wrapper.find('button').trigger('click');

        // Find the calories input and update its value
        const caloriesInput = wrapper.find('input[id="calories"]');
        await caloriesInput.setValue('240 cal');

        // Wait for the watch to trigger
        await nextTick();

        // Check if the update:modelValue event was emitted with the correct value
        const emittedEvents = wrapper.emitted('update:modelValue');
        expect(emittedEvents).toBeTruthy();
        expect(emittedEvents[0][0]).toEqual({ calories: '240 cal' });
    });

    it('displays existing nutrition information', async () => {
        const nutritionData = {
            calories: '240 cal',
            protein_content: '10g',
            carbohydrate_content: '37g',
        };

        const wrapper = mount(NutritionForm, {
            props: {
                modelValue: nutritionData,
            },
        });

        // Expand the form
        await wrapper.find('button').trigger('click');

        // Wait for the DOM to update
        await nextTick();

        // Check if the inputs have the correct values
        expect(wrapper.find('input[id="calories"]').element.value).toBe('240 cal');
        expect(wrapper.find('input[id="protein_content"]').element.value).toBe('10g');
        expect(wrapper.find('input[id="carbohydrate_content"]').element.value).toBe('37g');
    });
});
