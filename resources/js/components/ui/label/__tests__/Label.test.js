import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import { Label } from '..'

// Mock the cn utility function
vi.mock('@/lib/utils', () => ({
    cn: (...inputs) => inputs.filter(Boolean).join(' '),
}))

// Mock Radix Label component
vi.mock('radix-vue', () => ({
    Label: {
        name: 'RadixLabel',
        template: '<label :class="$attrs.class" :for="$attrs.for"><slot /></label>',
    },
}))

describe('Label.vue', () => {
    it('renders with default props', () => {
        const wrapper = mount(Label, {
            slots: {
                default: 'Username'
            }
        })

        expect(wrapper.text()).toBe('Username')
        expect(wrapper.attributes('class')).toContain('text-sm font-medium')
        expect(wrapper.attributes('class')).toContain('peer-disabled:cursor-not-allowed')
        expect(wrapper.attributes('class')).toContain('peer-disabled:opacity-70')
    })

    it('applies custom class', () => {
        const wrapper = mount(Label, {
            props: {
                class: 'custom-class'
            },
            slots: {
                default: 'Password'
            }
        })

        expect(wrapper.text()).toBe('Password')
        expect(wrapper.attributes('class')).toContain('custom-class')
        expect(wrapper.attributes('class')).toContain('text-sm font-medium')
    })

    it('forwards for attribute to the label element', () => {
        const wrapper = mount(Label, {
            props: {
                for: 'email-input'
            },
            slots: {
                default: 'Email'
            }
        })

        expect(wrapper.attributes('for')).toBe('email-input')
    })

    it('forwards other attributes to the label element', () => {
        const wrapper = mount(Label, {
            attrs: {
                id: 'label-id',
                'data-testid': 'email-label'
            },
            slots: {
                default: 'Email'
            }
        })

        expect(wrapper.attributes('id')).toBe('label-id')
        expect(wrapper.attributes('data-testid')).toBe('email-label')
    })

    it('renders with asChild prop', () => {
        const wrapper = mount(Label, {
            props: {
                asChild: true
            },
            slots: {
                default: 'Label with asChild'
            }
        })

        expect(wrapper.text()).toBe('Label with asChild')
        // asChild prop should be delegated to the radix component
        expect(wrapper.vm.delegatedProps.asChild).toBe(true)
    })

    it('renders with HTML content in slot', () => {
        const wrapper = mount(Label, {
            slots: {
                default: '<span>Label with <strong>HTML</strong></span>'
            }
        })

        expect(wrapper.html()).toContain('<span>Label with <strong>HTML</strong></span>')
    })
}) 