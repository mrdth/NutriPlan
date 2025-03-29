import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import { Button } from '..'

// Mock the cn utility function
vi.mock('@/lib/utils', () => ({
    cn: (...inputs) => inputs.join(' '),
}))

// Mock Primitive component from radix-vue
vi.mock('radix-vue', () => ({
    Primitive: {
        name: 'Primitive',
        template: '<component :is="$attrs.as || \'div\'" :class="$attrs.class"><slot /></component>',
    },
}))

describe('Button.vue', () => {
    it('renders with default props', () => {
        const wrapper = mount(Button, {
            slots: {
                default: 'Click me'
            }
        })

        expect(wrapper.text()).toBe('Click me')
        expect(wrapper.attributes('class')).toContain('bg-primary')
        expect(wrapper.attributes('class')).toContain('text-primary-foreground')
        expect(wrapper.attributes('class')).toContain('h-9 px-4 py-2')
    })

    it('applies custom class', () => {
        const wrapper = mount(Button, {
            props: {
                class: 'custom-class'
            },
            slots: {
                default: 'Click me'
            }
        })

        expect(wrapper.attributes('class')).toContain('custom-class')
    })

    it('renders with destructive variant', () => {
        const wrapper = mount(Button, {
            props: {
                variant: 'destructive'
            },
            slots: {
                default: 'Delete'
            }
        })

        expect(wrapper.text()).toBe('Delete')
        expect(wrapper.attributes('class')).toContain('bg-destructive')
        expect(wrapper.attributes('class')).toContain('text-destructive-foreground')
    })

    it('renders with outline variant', () => {
        const wrapper = mount(Button, {
            props: {
                variant: 'outline'
            }
        })

        expect(wrapper.attributes('class')).toContain('border')
        expect(wrapper.attributes('class')).toContain('border-input')
        expect(wrapper.attributes('class')).toContain('bg-background')
    })

    it('renders with secondary variant', () => {
        const wrapper = mount(Button, {
            props: {
                variant: 'secondary'
            }
        })

        expect(wrapper.attributes('class')).toContain('bg-secondary')
        expect(wrapper.attributes('class')).toContain('text-secondary-foreground')
    })

    it('renders with ghost variant', () => {
        const wrapper = mount(Button, {
            props: {
                variant: 'ghost'
            }
        })

        expect(wrapper.attributes('class')).toContain('hover:bg-accent')
        expect(wrapper.attributes('class')).toContain('hover:text-accent-foreground')
    })

    it('renders with link variant', () => {
        const wrapper = mount(Button, {
            props: {
                variant: 'link'
            }
        })

        expect(wrapper.attributes('class')).toContain('text-primary')
        expect(wrapper.attributes('class')).toContain('hover:underline')
    })

    it('renders with sm size', () => {
        const wrapper = mount(Button, {
            props: {
                size: 'sm'
            }
        })

        expect(wrapper.attributes('class')).toContain('h-8')
        expect(wrapper.attributes('class')).toContain('rounded-md')
        expect(wrapper.attributes('class')).toContain('px-3')
        expect(wrapper.attributes('class')).toContain('text-xs')
    })

    it('renders with lg size', () => {
        const wrapper = mount(Button, {
            props: {
                size: 'lg'
            }
        })

        expect(wrapper.attributes('class')).toContain('h-10')
        expect(wrapper.attributes('class')).toContain('rounded-md')
        expect(wrapper.attributes('class')).toContain('px-8')
    })

    it('renders with icon size', () => {
        const wrapper = mount(Button, {
            props: {
                size: 'icon'
            }
        })

        expect(wrapper.attributes('class')).toContain('h-9')
        expect(wrapper.attributes('class')).toContain('w-9')
    })

    it('renders as a different element', () => {
        const wrapper = mount(Button, {
            props: {
                as: 'a'
            }
        })

        expect(wrapper.element.tagName).toBe('A')
    })
}) 