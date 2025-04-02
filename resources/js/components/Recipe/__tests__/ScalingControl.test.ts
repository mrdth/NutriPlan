import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it } from 'vitest';
import ScalingControl from '../ScalingControl.vue';

describe('ScalingControl', () => {
    let wrapper: ReturnType<typeof mount>;

    beforeEach(() => {
        wrapper = mount(ScalingControl, {
            props: {
                originalServings: 4,
            },
        });
    });

    it('renders correctly with initial props', () => {
        const input = wrapper.find('input[type="number"]').element as HTMLInputElement;
        expect(input).toBeTruthy();
        expect(input.value).toBe('4');
    });

    it('increases servings by 0.5 when plus button is clicked', async () => {
        const plusButton = wrapper.findAll('button')[1]; // Second button is plus
        await plusButton.trigger('click');

        const input = wrapper.find('input[type="number"]').element as HTMLInputElement;
        expect(input.value).toBe('4.5');
        expect(wrapper.emitted('update:scalingFactor')).toBeTruthy();
        const emittedEvents = wrapper.emitted('update:scalingFactor');
        if (emittedEvents) {
            expect(emittedEvents[0]).toEqual([1.125]); // 4.5/4 = 1.125
        }
    });

    it('decreases servings by 0.5 when minus button is clicked', async () => {
        // First increase to 4.5
        const plusButton = wrapper.findAll('button')[1];
        await plusButton.trigger('click');

        // Then decrease back to 4
        const minusButton = wrapper.findAll('button')[0];
        await minusButton.trigger('click');

        const input = wrapper.find('input[type="number"]').element as HTMLInputElement;
        expect(input.value).toBe('4');
        const emittedEvents = wrapper.emitted('update:scalingFactor');
        if (emittedEvents) {
            expect(emittedEvents[1]).toEqual([1]); // 4/4 = 1
        }
    });

    it('disables minus button when servings is 0.5', async () => {
        // Create a new wrapper with servings set to 0.5
        const localWrapper = mount(ScalingControl, {
            props: {
                originalServings: 0.5,
            },
        });

        // Verify the input shows 0.5
        const input = localWrapper.find('input[type="number"]').element as HTMLInputElement;
        expect(input.value).toBe('0.5');

        // Try to click the minus button
        const minusButton = localWrapper.findAll('button')[0];
        await minusButton.trigger('click');

        // The servings should still be 0.5 since the button is disabled
        expect(input.value).toBe('0.5');
    });

    it('resets to original servings when reset button is clicked', async () => {
        // First change servings to something else
        await wrapper.find('input[type="number"]').setValue(6);
        await wrapper.find('input[type="number"]').trigger('change');

        // Verify the "Recipe scaled" indicator is shown
        expect(wrapper.text()).toContain('Recipe scaled');

        // Click reset button
        const resetButton = wrapper.find('button.text-blue-600');
        await resetButton.trigger('click');

        const input = wrapper.find('input[type="number"]').element as HTMLInputElement;
        expect(input.value).toBe('4');
        expect(wrapper.emitted('update:scalingFactor')).toBeTruthy();
        // The last emitted value should be 1 (reset to original)
        const emittedEvents = wrapper.emitted('update:scalingFactor');
        if (emittedEvents) {
            expect(emittedEvents[emittedEvents.length - 1]).toEqual([1]);
        }
    });

    it('prevents servings from being less than 0.5', async () => {
        await wrapper.find('input[type="number"]').setValue(0);
        await wrapper.find('input[type="number"]').trigger('change');

        const input = wrapper.find('input[type="number"]').element as HTMLInputElement;
        expect(input.value).toBe('0.5');
    });

    it('handles non-numeric input by resetting to original servings', async () => {
        // First set to a different value
        await wrapper.find('input[type="number"]').setValue(6);
        await wrapper.find('input[type="number"]').trigger('change');

        // Then try to set an invalid value
        await wrapper.find('input[type="number"]').setValue('');
        await wrapper.find('input[type="number"]').trigger('change');

        // Should reset to original
        expect(wrapper.emitted('update:scalingFactor')).toBeTruthy();
    });

    it('emits the correct scaling factor when servings change', async () => {
        // Set servings to 6
        await wrapper.find('input[type="number"]').setValue(6);
        await wrapper.find('input[type="number"]').trigger('change');

        // Check that the correct scaling factor was emitted (6/4 = 1.5)
        const emittedEvents = wrapper.emitted('update:scalingFactor');
        if (emittedEvents) {
            expect(emittedEvents[emittedEvents.length - 1][0]).toBeCloseTo(1.5);
        }
    });

    it('handles NaN values by resetting to the minimum value', async () => {
        // Set an invalid string that would result in NaN when converted to number
        const input = wrapper.find('input[type="number"]');
        await input.setValue('abc');
        await input.trigger('change');

        // Verify that servings are reset to 0.5 (the minimum value)
        const inputElement = input.element as HTMLInputElement;
        expect(inputElement.value).toBe('0.5');

        // Try another invalid case with invalid number format
        await input.setValue('123abc');
        await input.trigger('change');

        // Verify that servings are reset to 0.5 (the minimum value)
        expect(inputElement.value).toBe('0.5');
    });

    it('correctly handles direct input of valid numbers', async () => {
        // Directly set a valid number through model update
        await wrapper.find('input[type="number"]').setValue('6');
        await wrapper.find('input[type="number"]').trigger('change');

        const input = wrapper.find('input[type="number"]').element as HTMLInputElement;
        expect(input.value).toBe('6');

        // Check that the correct scaling factor was emitted (6/4 = 1.5)
        const emittedEvents = wrapper.emitted('update:scalingFactor');
        if (emittedEvents) {
            expect(emittedEvents[emittedEvents.length - 1][0]).toBeCloseTo(1.5);
        }

        // The recipe scaled indicator should be shown
        expect(wrapper.text()).toContain('Recipe scaled');
    });
});
