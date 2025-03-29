import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import Heading from '../Heading.vue'

describe('Heading.vue', () => {
    it('renders the title correctly', () => {
        const title = 'Test Title'
        const wrapper = mount(Heading, {
            props: {
                title
            }
        })

        expect(wrapper.find('h2').exists()).toBe(true)
        expect(wrapper.find('h2').text()).toBe(title)
        expect(wrapper.find('h2').classes()).toContain('text-xl')
        expect(wrapper.find('h2').classes()).toContain('font-semibold')
        expect(wrapper.find('h2').classes()).toContain('tracking-tight')
    })

    it('does not render description when not provided', () => {
        const wrapper = mount(Heading, {
            props: {
                title: 'Test Title'
            }
        })

        expect(wrapper.find('p').exists()).toBe(false)
    })

    it('renders description when provided', () => {
        const title = 'Test Title'
        const description = 'Test Description'
        const wrapper = mount(Heading, {
            props: {
                title,
                description
            }
        })

        expect(wrapper.find('p').exists()).toBe(true)
        expect(wrapper.find('p').text()).toBe(description)
        expect(wrapper.find('p').classes()).toContain('text-sm')
        expect(wrapper.find('p').classes()).toContain('text-muted-foreground')
    })

    it('applies correct container classes', () => {
        const wrapper = mount(Heading, {
            props: {
                title: 'Test Title'
            }
        })

        expect(wrapper.find('div').classes()).toContain('mb-8')
        expect(wrapper.find('div').classes()).toContain('space-y-0.5')
    })
}) 