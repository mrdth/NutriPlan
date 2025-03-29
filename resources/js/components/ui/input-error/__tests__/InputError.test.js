import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import { InputError } from '..'

describe('InputError.vue', () => {
    it('renders the error message when provided', () => {
        const errorMessage = 'This field is required'
        const wrapper = mount(InputError, {
            props: {
                message: errorMessage
            }
        })

        expect(wrapper.text()).toBe(errorMessage)
        expect(wrapper.find('p').exists()).toBe(true)
        expect(wrapper.find('p').classes()).toContain('text-red-600')
    })

    it('renders a v-if comment when no message is provided', () => {
        const wrapper = mount(InputError)

        expect(wrapper.find('p').exists()).toBe(false)
        // Check for the v-if comment placeholder
        expect(wrapper.html()).toContain('<!--v-if-->')
    })

    it('does not render p element when message is empty string', () => {
        const wrapper = mount(InputError, {
            props: {
                message: ''
            }
        })

        // The component should not render the paragraph with an empty string
        expect(wrapper.find('p').exists()).toBe(false)
        expect(wrapper.html()).toContain('<!--v-if-->')
    })

    it('updates when the message prop changes', async () => {
        const wrapper = mount(InputError)

        // Initially no p element
        expect(wrapper.find('p').exists()).toBe(false)

        // Update the prop
        await wrapper.setProps({ message: 'New error message' })

        // Now the message should be displayed
        expect(wrapper.find('p').exists()).toBe(true)
        expect(wrapper.text()).toBe('New error message')

        // Update to empty message
        await wrapper.setProps({ message: '' })

        // Should not render the p element
        expect(wrapper.find('p').exists()).toBe(false)
        expect(wrapper.html()).toContain('<!--v-if-->')

        // Add a message again
        await wrapper.setProps({ message: 'Another message' })

        // Should render the p element
        expect(wrapper.find('p').exists()).toBe(true)
        expect(wrapper.text()).toBe('Another message')
    })
}) 