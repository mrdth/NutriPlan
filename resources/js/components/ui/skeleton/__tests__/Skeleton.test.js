import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import { Skeleton } from '..'

// Mock the cn utility function
vi.mock('@/lib/utils', () => ({
    cn: (...inputs) => inputs.filter(Boolean).join(' '),
}))

describe('Skeleton.vue', () => {
    it('renders with default props', () => {
        const wrapper = mount(Skeleton)

        expect(wrapper.exists()).toBe(true)
        expect(wrapper.attributes('class')).toContain('animate-pulse rounded-md bg-muted')
    })

    it('applies custom class', () => {
        const wrapper = mount(Skeleton, {
            props: {
                class: 'h-10 w-20 custom-class'
            }
        })

        expect(wrapper.attributes('class')).toContain('animate-pulse rounded-md bg-muted')
        expect(wrapper.attributes('class')).toContain('h-10 w-20 custom-class')
    })

    it('renders as a div element', () => {
        const wrapper = mount(Skeleton)

        expect(wrapper.element.tagName).toBe('DIV')
    })

    it('accepts and applies attributes', () => {
        const wrapper = mount(Skeleton, {
            attrs: {
                id: 'skeleton-id',
                'data-testid': 'skeleton-element',
                'aria-hidden': 'true'
            }
        })

        expect(wrapper.attributes('id')).toBe('skeleton-id')
        expect(wrapper.attributes('data-testid')).toBe('skeleton-element')
        expect(wrapper.attributes('aria-hidden')).toBe('true')
    })
}) 