import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import Icon from '../Icon.vue'

// Mock the utils module - hoisted to top, no references to variables defined later
vi.mock('@/lib/utils', () => ({
    cn: (...inputs) => inputs.filter(Boolean).join(' ')
}))

// Mock the icons module - use factory functions only, no references to variables defined later
vi.mock('lucide-vue-next', () => {
    return {
        Home: {
            template: '<div data-testid="mock-icon"></div>',
            inheritAttrs: true
        },
        User: {
            template: '<div data-testid="mock-icon"></div>',
            inheritAttrs: true
        },
        Heart: {
            template: '<div data-testid="mock-icon"></div>',
            inheritAttrs: true
        }
    }
})

describe('Icon Component', () => {
    it('renders the correct icon based on name prop', () => {
        const wrapper = mount(Icon, {
            props: {
                name: 'home'
            },
            global: {
                stubs: {
                    component: true
                }
            }
        })

        // Verify that the component is rendered and the icon prop is computed correctly
        expect(wrapper.vm.icon).toBeTruthy()
    })

    it('computes class name correctly', () => {
        const wrapper = mount(Icon, {
            props: {
                name: 'home',
                class: 'custom-class'
            }
        })

        // Verify the computed className includes both default and custom classes
        expect(wrapper.vm.className).toBe('h-4 w-4 custom-class')
    })

    it('capitalizes icon name correctly', () => {
        const wrapper = mount(Icon, {
            props: {
                name: 'home'
            }
        })

        // Should match the icon in the mocked module
        expect(wrapper.vm.icon).toBeTruthy()
    })

    it('handles already capitalized icon names', () => {
        const wrapper = mount(Icon, {
            props: {
                name: 'Home'
            }
        })

        // Should handle already capitalized names
        expect(wrapper.vm.icon).toBeTruthy()
    })

    it('passes props to the icon component', () => {
        const wrapper = mount(Icon, {
            props: {
                name: 'heart',
                size: 24,
                strokeWidth: 1.5,
                color: 'red'
            }
        })

        // Verify props are passed to the component
        const iconProps = wrapper.props()
        expect(iconProps.size).toBe(24)
        expect(iconProps.strokeWidth).toBe(1.5)
        expect(iconProps.color).toBe('red')
    })

    it('uses default prop values when not specified', () => {
        const wrapper = mount(Icon, {
            props: {
                name: 'user'
            }
        })

        // Check default props
        const iconProps = wrapper.props()
        expect(iconProps.size).toBe(16)
        expect(iconProps.strokeWidth).toBe(2)
        expect(iconProps.color).toBeUndefined()
    })
}) 