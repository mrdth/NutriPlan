import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import { Separator } from '..'

// Mock the cn utility function
vi.mock('@/lib/utils', () => ({
    cn: (...inputs) => inputs.filter(Boolean).join(' '),
}))

// Mock Radix Separator component
vi.mock('radix-vue', () => ({
    Separator: {
        name: 'RadixSeparator',
        template: '<div :class="$attrs.class" :data-orientation="orientation"><slot /></div>',
        props: {
            orientation: {
                type: String,
                default: 'horizontal'
            }
        }
    },
}))

describe('Separator.vue', () => {
    it('renders with default props (horizontal orientation)', () => {
        const wrapper = mount(Separator)

        expect(wrapper.attributes('class')).toContain('relative shrink-0 bg-border')
        expect(wrapper.attributes('class')).toContain('h-px w-full')
        expect(wrapper.attributes('data-orientation')).toBe('horizontal')
    })

    it('renders with vertical orientation', () => {
        const wrapper = mount(Separator, {
            props: {
                orientation: 'vertical'
            }
        })

        expect(wrapper.attributes('class')).toContain('relative shrink-0 bg-border')
        expect(wrapper.attributes('class')).toContain('h-full w-px')
        expect(wrapper.attributes('data-orientation')).toBe('vertical')
    })

    it('applies custom class', () => {
        const wrapper = mount(Separator, {
            props: {
                class: 'custom-class'
            }
        })

        expect(wrapper.attributes('class')).toContain('custom-class')
        expect(wrapper.attributes('class')).toContain('relative shrink-0 bg-border')
    })

    it('renders with label in horizontal orientation', () => {
        const wrapper = mount(Separator, {
            props: {
                label: 'OR'
            }
        })

        expect(wrapper.text()).toBe('OR')
        const labelElement = wrapper.find('span')
        expect(labelElement.exists()).toBe(true)
        expect(labelElement.attributes('class')).toContain('absolute left-1/2 top-1/2 flex -translate-x-1/2 -translate-y-1/2')
        expect(labelElement.attributes('class')).toContain('h-[1px] px-2 py-1')
    })

    it('renders with label in vertical orientation', () => {
        const wrapper = mount(Separator, {
            props: {
                label: 'OR',
                orientation: 'vertical'
            }
        })

        expect(wrapper.text()).toBe('OR')
        const labelElement = wrapper.find('span')
        expect(labelElement.exists()).toBe(true)
        expect(labelElement.attributes('class')).toContain('absolute left-1/2 top-1/2 flex -translate-x-1/2 -translate-y-1/2')
        expect(labelElement.attributes('class')).toContain('w-[1px] px-1 py-2')
    })

    it('does not render label element when label prop is not provided', () => {
        const wrapper = mount(Separator)

        const labelElement = wrapper.find('span')
        expect(labelElement.exists()).toBe(false)
    })
}) 