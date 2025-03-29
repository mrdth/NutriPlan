import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import { Textarea } from '..'

describe('Textarea.vue', () => {
    it('renders with default props', () => {
        const wrapper = mount(Textarea, {
            props: {
                modelValue: ''
            }
        })

        expect(wrapper.exists()).toBe(true)
        expect(wrapper.attributes('class')).toContain('block w-full rounded-md border-0')
    })

    it('renders with initial value', () => {
        const wrapper = mount(Textarea, {
            props: {
                modelValue: 'Initial text'
            }
        })

        expect(wrapper.element.value).toBe('Initial text')
    })

    it('emits update:modelValue event when input changes', async () => {
        const wrapper = mount(Textarea, {
            props: {
                modelValue: ''
            }
        })

        await wrapper.setValue('New text')

        expect(wrapper.emitted('update:modelValue')).toBeTruthy()
        expect(wrapper.emitted('update:modelValue')[0]).toEqual(['New text'])
    })

    it('accepts and applies attributes', () => {
        const wrapper = mount(Textarea, {
            props: {
                modelValue: ''
            },
            attrs: {
                placeholder: 'Enter some text',
                rows: '5',
                disabled: true,
                'data-testid': 'text-input'
            }
        })

        expect(wrapper.attributes('placeholder')).toBe('Enter some text')
        expect(wrapper.attributes('rows')).toBe('5')
        expect(wrapper.attributes('disabled')).toBe('')
        expect(wrapper.attributes('data-testid')).toBe('text-input')
    })

    it('updates value when model value prop changes', async () => {
        const wrapper = mount(Textarea, {
            props: {
                modelValue: 'Initial text'
            }
        })

        expect(wrapper.element.value).toBe('Initial text')

        await wrapper.setProps({
            modelValue: 'Updated text'
        })

        expect(wrapper.element.value).toBe('Updated text')
    })
}) 