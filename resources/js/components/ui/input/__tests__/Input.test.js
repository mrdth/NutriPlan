import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import { Input } from '..'

// Mock the cn utility function
vi.mock('@/lib/utils', () => ({
    cn: (...inputs) => inputs.filter(Boolean).join(' '),
}))

describe('Input.vue', () => {
    it('renders with default props', () => {
        const wrapper = mount(Input)

        expect(wrapper.find('input').exists()).toBe(true)
        expect(wrapper.attributes('class')).toContain('flex h-10 w-full rounded-md border')
    })

    it('applies custom class', () => {
        const wrapper = mount(Input, {
            props: {
                class: 'custom-class'
            }
        })

        expect(wrapper.attributes('class')).toContain('custom-class')
    })

    it('handles v-model binding', async () => {
        const wrapper = mount(Input, {
            props: {
                modelValue: 'initial value',
                'onUpdate:modelValue': (e) => wrapper.setProps({ modelValue: e })
            }
        })

        expect(wrapper.element.value).toBe('initial value')

        await wrapper.setValue('new value')
        expect(wrapper.emitted('update:modelValue')).toBeTruthy()
        expect(wrapper.emitted('update:modelValue')[0]).toEqual(['new value'])
    })

    it('uses defaultValue when modelValue is not provided', () => {
        const wrapper = mount(Input, {
            props: {
                defaultValue: 'default text'
            }
        })

        expect(wrapper.element.value).toBe('default text')
    })

    it('accepts number as input value', async () => {
        const wrapper = mount(Input, {
            props: {
                modelValue: 42,
                'onUpdate:modelValue': (e) => wrapper.setProps({ modelValue: e })
            }
        })

        expect(wrapper.element.value).toBe('42')

        await wrapper.setValue('100')
        expect(wrapper.emitted('update:modelValue')).toBeTruthy()
        expect(wrapper.emitted('update:modelValue')[0]).toEqual(['100'])
    })

    it('preserves attributes like type, placeholder, disabled', () => {
        const wrapper = mount(Input, {
            attrs: {
                type: 'password',
                placeholder: 'Enter password',
                disabled: true
            }
        })

        expect(wrapper.attributes('type')).toBe('password')
        expect(wrapper.attributes('placeholder')).toBe('Enter password')
        expect(wrapper.attributes('disabled')).toBe('')
    })

    it('works with browser events', async () => {
        const onFocus = vi.fn()
        const onBlur = vi.fn()

        const wrapper = mount(Input, {
            attrs: {
                onFocus,
                onBlur
            }
        })

        await wrapper.trigger('focus')
        expect(onFocus).toHaveBeenCalled()

        await wrapper.trigger('blur')
        expect(onBlur).toHaveBeenCalled()
    })
}) 