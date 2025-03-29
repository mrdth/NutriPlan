import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import NutritionInformation from '../NutritionInformation.vue';

describe('NutritionInformation.vue', () => {
    it('renders nutrition information when provided', () => {
        const nutrition = {
            calories: '240 cal',
            carbohydrate_content: '37g',
            protein_content: '4g',
            fat_content: '9g',
        };

        const wrapper = mount(NutritionInformation, {
            props: {
                nutrition,
            },
        });

        expect(wrapper.text()).toContain('Calories');
        expect(wrapper.text()).toContain('240 cal');
        expect(wrapper.text()).toContain('Carbohydrates');
        expect(wrapper.text()).toContain('37g');
        expect(wrapper.text()).toContain('Protein');
        expect(wrapper.text()).toContain('4g');
        expect(wrapper.text()).toContain('Fat');
        expect(wrapper.text()).toContain('9g');
    });

    it('displays a message when no nutrition information is available', () => {
        const wrapper = mount(NutritionInformation, {
            props: {
                nutrition: null,
            },
        });

        expect(wrapper.text()).toContain('No nutrition information available');
    });

    it('only renders fields that are present in the nutrition data', () => {
        const nutrition = {
            calories: '240 cal',
            // No other fields provided
        };

        const wrapper = mount(NutritionInformation, {
            props: {
                nutrition,
            },
        });

        expect(wrapper.text()).toContain('Calories');
        expect(wrapper.text()).toContain('240 cal');
        expect(wrapper.text()).not.toContain('Carbohydrates');
        expect(wrapper.text()).not.toContain('Protein');
    });
});
