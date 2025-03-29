import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import { Toggle } from '..'

// Mock Radix-Vue Toggle component
vi.mock('radix-vue', () => ({
    Toggle: {
        name: 'RadixToggle',
        template: '<button :class="$attrs.class" :data-state="pressed ? \'on\' : \'off\'" @click="$emit(\'update:pressed\', !pressed)"><slot /></button>',
        props: {
            pressed: {
                type: Boolean,
                default: false
            }
        },
        emits: ['update:pressed']
    }
}))

// Mock the cn utility function
vi.mock('@/lib/utils', () => ({
    cn: (...inputs) => inputs.filter(Boolean).join(' '),
}))

describe('Toggle.vue', () => {
    it('renders with default props and slot content', () => {
        const wrapper = mount(Toggle, {
            slots: {
                default: 'Toggle me'
            }
        })

        expect(wrapper.text()).toBe('Toggle me')
        expect(wrapper.attributes('class')).toContain('inline-flex items-center justify-center')
        expect(wrapper.attributes('class')).toContain('rounded-md font-medium')
        expect(wrapper.attributes('data-state')).toBe('off')
    })

    it('applies correct styling based on pressed state', async () => {
        const wrapper = mount(Toggle, {
            props: {
                pressed: true
            },
            slots: {
                default: 'Pressed Toggle'
            }
        })

        expect(wrapper.attributes('data-state')).toBe('on')
        expect(wrapper.text()).toBe('Pressed Toggle')
    })

    it('emits update:pressed event when clicked', async () => {
        const wrapper = mount(Toggle, {
            props: {
                pressed: false
            }
        })

        await wrapper.find('button').trigger('click')

        expect(wrapper.emitted('update:pressed')).toBeTruthy()
        expect(wrapper.emitted('update:pressed')[0]).toEqual([true])
    })

    it('applies outline variant class', () => {
        const wrapper = mount(Toggle, {
            props: {
                variant: 'outline'
            }
        })

        expect(wrapper.attributes('class')).toContain('border border-input bg-transparent')
        expect(wrapper.attributes('class')).toContain('hover:bg-accent hover:text-accent-foreground')
    })

    it('applies small size class', () => {
        const wrapper = mount(Toggle, {
            props: {
                size: 'sm'
            }
        })

        expect(wrapper.attributes('class')).toContain('h-9 px-3')
    })

    it('applies large size class', () => {
        const wrapper = mount(Toggle, {
            props: {
                size: 'lg'
            }
        })

        expect(wrapper.attributes('class')).toContain('h-11 px-5')
    })

    it('applies custom class', () => {
        const wrapper = mount(Toggle, {
            props: {
                className: 'my-custom-class'
            }
        })

        expect(wrapper.attributes('class')).toContain('my-custom-class')
    })

    it('forwards native attributes', () => {
        const wrapper = mount(Toggle, {
            attrs: {
                id: 'test-toggle',
                'data-testid': 'toggle-component',
                'aria-label': 'Toggle button'
            }
        })

        expect(wrapper.attributes('id')).toBe('test-toggle')
        expect(wrapper.attributes('data-testid')).toBe('toggle-component')
        expect(wrapper.attributes('aria-label')).toBe('Toggle button')
    })

    it('works with v-model binding', async () => {
        const wrapper = mount({
            template: '<Toggle v-model:pressed="isPressed" />',
            components: { Toggle },
            data() {
                return {
                    isPressed: false
                }
            }
        })

        const toggle = wrapper.findComponent(Toggle)

        // Initial state
        expect(toggle.props('pressed')).toBe(false)

        // Click the toggle
        await toggle.find('button').trigger('click')

        // v-model should update the parent state
        expect(wrapper.vm.isPressed).toBe(true)

        // Toggle should reflect the new state
        expect(toggle.emitted('update:pressed')).toBeTruthy()
        expect(toggle.emitted('update:pressed')[0]).toEqual([true])
    })
}) 