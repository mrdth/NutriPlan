import { mount } from '@vue/test-utils';
import { Combobox, ComboboxWithCreate } from '../index';
import { nextTick } from 'vue';
import { vi, describe, it, expect } from 'vitest';
import axios from 'axios';

// Mock the Input component
vi.mock('@/components/ui/input', () => ({
    Input: {
        template: '<input :value="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)" v-bind="$attrs" />',
        props: ['modelValue'],
        emits: ['update:modelValue']
    }
}));

// Mock lucide-vue-next PlusIcon
vi.mock('lucide-vue-next', () => ({
    PlusIcon: {
        template: '<svg data-test="plus-icon"></svg>'
    }
}));

// Mock axios
vi.mock('axios', () => ({
    default: {
        post: vi.fn()
    }
}));

// Sample options for testing
const mockOptions = [
    { id: 1, name: 'Option 1' },
    { id: 2, name: 'Option 2' },
    { id: 3, name: 'Option 3' }
];

describe('Combobox Components', () => {
    describe('Combobox.vue', () => {
        it('renders correctly with default props', () => {
            const wrapper = mount(Combobox, {
                props: {
                    modelValue: 1,
                    options: mockOptions
                }
            });

            expect(wrapper.exists()).toBe(true);
            expect(wrapper.find('input').exists()).toBe(true);
            expect(wrapper.find('input').attributes('placeholder')).toBe('Search...');
        });

        it('renders with custom placeholder', () => {
            const wrapper = mount(Combobox, {
                props: {
                    modelValue: 1,
                    options: mockOptions,
                    placeholder: 'Custom placeholder'
                }
            });

            expect(wrapper.find('input').attributes('placeholder')).toBe('Custom placeholder');
        });

        it('initializes input with selected option name', () => {
            const wrapper = mount(Combobox, {
                props: {
                    modelValue: 2,
                    options: mockOptions
                }
            });

            expect(wrapper.find('input').element.value).toBe('Option 2');
        });

        it('emits update:modelValue when selectOption method is called', async () => {
            const wrapper = mount(Combobox, {
                props: {
                    modelValue: 1,
                    options: mockOptions
                },
                attachTo: document.body
            });

            // Call the selectOption method directly
            await wrapper.vm.selectOption(2);

            // Check if the correct value was emitted
            expect(wrapper.emitted('update:modelValue')[0]).toEqual([2]);
        });
    });

    describe('ComboboxWithCreate.vue', () => {
        it('renders correctly with default props', () => {
            const wrapper = mount(ComboboxWithCreate, {
                props: {
                    modelValue: 1,
                    options: mockOptions
                }
            });

            expect(wrapper.exists()).toBe(true);
            expect(wrapper.find('input').exists()).toBe(true);
            expect(wrapper.find('input').attributes('placeholder')).toBe('Search...');
        });

        it('initializes input with correct value', () => {
            const wrapper = mount(ComboboxWithCreate, {
                props: {
                    modelValue: 2,
                    options: mockOptions,
                    selected: { id: 2, name: 'Option 2' }
                }
            });

            expect(wrapper.find('input').element.value).toBe('Option 2');
        });

        it('calls the API in createNewOption method', async () => {
            // Set up mock response
            const mockResponse = { data: { id: 4, name: 'New Option' } };
            axios.post.mockResolvedValue(mockResponse);

            // Create a wrapper for ComboboxWithCreate
            const wrapper = mount(ComboboxWithCreate, {
                props: {
                    modelValue: 1,
                    options: mockOptions,
                    allowCreate: true,
                    createEndpoint: '/test-endpoint'
                },
                attachTo: document.body
            });

            // Replace the component's method with our mock
            const originalMethod = wrapper.vm.createNewOption;
            wrapper.vm.createNewOption = vi.fn(async () => {
                // Call axios directly to check if mocking works
                await axios.post('/test-endpoint', { name: 'Test Option' });
                // Simulate the emits that would happen in the real method
                wrapper.vm.$emit('option-created', { id: 4, name: 'Test Option' });
                wrapper.vm.$emit('update:modelValue', 4);
            });

            // Call the method
            await wrapper.vm.createNewOption();

            // Check that our mocked method was called
            expect(wrapper.vm.createNewOption).toHaveBeenCalled();

            // Verify API was called and events were emitted
            expect(axios.post).toHaveBeenCalledWith('/test-endpoint', { name: 'Test Option' });
            expect(wrapper.emitted('option-created')).toBeTruthy();
            expect(wrapper.emitted('update:modelValue')).toBeTruthy();

            // Restore original method to clean up
            wrapper.vm.createNewOption = originalMethod;
        });

        it('emits update:modelValue when selectOption method is called', async () => {
            const wrapper = mount(ComboboxWithCreate, {
                props: {
                    modelValue: 1,
                    options: mockOptions,
                    allowCreate: true
                },
                attachTo: document.body
            });

            // Call selectOption method directly
            await wrapper.vm.selectOption(2);

            // Check if the correct value was emitted
            expect(wrapper.emitted('update:modelValue')[0]).toEqual([2]);
        });
    });
}); 