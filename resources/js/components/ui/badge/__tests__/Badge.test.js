import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import { Badge } from '..'

describe('Badge.vue', () => {
    it('renders with default props', () => {
        const wrapper = mount(Badge, {
            slots: {
                default: 'New'
            }
        })

        expect(wrapper.text()).toBe('New')
        expect(wrapper.attributes('class')).toContain('inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold')
        expect(wrapper.attributes('class')).toContain('border-transparent bg-primary text-primary-foreground')
    })

    it('renders with variant=secondary', () => {
        const wrapper = mount(Badge, {
            props: {
                variant: 'secondary'
            },
            slots: {
                default: 'Secondary'
            }
        })

        expect(wrapper.text()).toBe('Secondary')
        expect(wrapper.attributes('class')).toContain('border-transparent bg-secondary text-secondary-foreground')
    })

    it('renders with variant=destructive', () => {
        const wrapper = mount(Badge, {
            props: {
                variant: 'destructive'
            },
            slots: {
                default: 'Destructive'
            }
        })

        expect(wrapper.text()).toBe('Destructive')
        expect(wrapper.attributes('class')).toContain('border-transparent bg-destructive text-destructive-foreground')
    })

    it('renders with variant=outline', () => {
        const wrapper = mount(Badge, {
            props: {
                variant: 'outline'
            },
            slots: {
                default: 'Outline'
            }
        })

        expect(wrapper.text()).toBe('Outline')
        expect(wrapper.attributes('class')).toContain('text-foreground')
    })

    it('applies custom class', () => {
        const wrapper = mount(Badge, {
            props: {
                class: 'custom-class'
            },
            slots: {
                default: 'Custom'
            },
            attrs: {
                'data-testid': 'badge'
            }
        })

        expect(wrapper.text()).toBe('Custom')
        expect(wrapper.attributes('class')).toContain('custom-class')
        expect(wrapper.attributes('data-testid')).toBe('badge')
    })

    it('renders with HTML content in slot', () => {
        const wrapper = mount(Badge, {
            slots: {
                default: '<span>Badge with <strong>HTML</strong></span>'
            }
        })

        expect(wrapper.html()).toContain('<span>Badge with <strong>HTML</strong></span>')
    })
}) 