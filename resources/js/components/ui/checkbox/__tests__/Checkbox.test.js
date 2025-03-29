import { mount } from '@vue/test-utils';
import { Checkbox } from '../index';
import { nextTick } from 'vue';
import { vi } from 'vitest';

// Create stubs for Radix Vue components
const CheckboxRootStub = {
    template: '<div :class="$attrs.class" v-bind="$attrs"><slot /></div>',
    inheritAttrs: false
};

const CheckboxIndicatorStub = {
    template: '<div class="checkbox-indicator"><slot /></div>',
    inheritAttrs: false
};

describe('Checkbox.vue', () => {
    it('renders correctly', () => {
        const wrapper = mount(Checkbox, {
            global: {
                stubs: {
                    CheckboxRoot: CheckboxRootStub,
                    CheckboxIndicator: CheckboxIndicatorStub
                }
            }
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('applies custom class', () => {
        const wrapper = mount(Checkbox, {
            props: {
                class: 'custom-checkbox'
            },
            global: {
                stubs: {
                    CheckboxRoot: CheckboxRootStub,
                    CheckboxIndicator: CheckboxIndicatorStub
                }
            }
        });
        expect(wrapper.classes()).toContain('custom-checkbox');
    });

    it('forwards disabled prop', () => {
        const wrapper = mount(Checkbox, {
            props: {
                disabled: true
            },
            global: {
                stubs: {
                    CheckboxRoot: CheckboxRootStub,
                    CheckboxIndicator: CheckboxIndicatorStub
                }
            }
        });
        // Check if the disabled prop is passed to the CheckboxRoot component
        expect(wrapper.attributes()).toHaveProperty('disabled');
    });

    it('renders slot content', () => {
        const wrapper = mount(Checkbox, {
            slots: {
                default: '<span data-test="custom-check">✓</span>'
            },
            global: {
                stubs: {
                    CheckboxRoot: CheckboxRootStub,
                    CheckboxIndicator: CheckboxIndicatorStub
                }
            }
        });

        expect(wrapper.find('[data-test="custom-check"]').exists()).toBe(true);
    });

    it('includes appropriate styling classes', () => {
        const wrapper = mount(Checkbox, {
            global: {
                stubs: {
                    CheckboxRoot: CheckboxRootStub,
                    CheckboxIndicator: CheckboxIndicatorStub
                }
            }
        });

        const classes = wrapper.attributes('class').split(' ');
        expect(classes.some(cls => cls.includes('rounded-sm'))).toBe(true);
        expect(classes.some(cls => cls.includes('border'))).toBe(true);
        expect(classes.some(cls => cls.includes('data-[state=checked]:bg-primary'))).toBe(true);
    });
}); 