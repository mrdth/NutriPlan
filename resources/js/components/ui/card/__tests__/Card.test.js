import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle
} from '..'

// Mock the cn utility function
vi.mock('@/lib/utils', () => ({
    cn: (...inputs) => inputs.filter(Boolean).join(' '),
}))

describe('Card Components', () => {
    // Card tests
    describe('Card.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(Card)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.attributes('class')).toContain('rounded-lg border bg-card text-card-foreground shadow-sm')
        })

        it('renders slot content', () => {
            const wrapper = mount(Card, {
                slots: {
                    default: '<div data-testid="card-content">Test Content</div>'
                }
            })

            expect(wrapper.find('[data-testid="card-content"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="card-content"]').text()).toBe('Test Content')
        })

        it('applies custom class', () => {
            const wrapper = mount(Card, {
                props: {
                    class: 'custom-class'
                }
            })

            expect(wrapper.attributes('class')).toContain('custom-class')
            expect(wrapper.attributes('class')).toContain('rounded-lg border bg-card text-card-foreground shadow-sm')
        })
    })

    // CardHeader tests
    describe('CardHeader.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(CardHeader)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.attributes('class')).toContain('flex flex-col gap-y-1.5 p-6')
        })

        it('renders slot content', () => {
            const wrapper = mount(CardHeader, {
                slots: {
                    default: '<div data-testid="header-content">Header Content</div>'
                }
            })

            expect(wrapper.find('[data-testid="header-content"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="header-content"]').text()).toBe('Header Content')
        })

        it('applies custom class', () => {
            const wrapper = mount(CardHeader, {
                props: {
                    class: 'custom-header-class'
                }
            })

            expect(wrapper.attributes('class')).toContain('custom-header-class')
            expect(wrapper.attributes('class')).toContain('flex flex-col gap-y-1.5 p-6')
        })
    })

    // CardContent tests
    describe('CardContent.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(CardContent)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.attributes('class')).toContain('p-6 pt-0')
        })

        it('renders slot content', () => {
            const wrapper = mount(CardContent, {
                slots: {
                    default: '<div data-testid="content">Content</div>'
                }
            })

            expect(wrapper.find('[data-testid="content"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="content"]').text()).toBe('Content')
        })

        it('applies custom class', () => {
            const wrapper = mount(CardContent, {
                props: {
                    class: 'custom-content-class'
                }
            })

            expect(wrapper.attributes('class')).toContain('custom-content-class')
            expect(wrapper.attributes('class')).toContain('p-6 pt-0')
        })
    })

    // CardTitle tests
    describe('CardTitle.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(CardTitle)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.element.tagName).toBe('H3')
            expect(wrapper.attributes('class')).toContain('text-2xl font-semibold leading-none tracking-tight')
        })

        it('renders slot content', () => {
            const wrapper = mount(CardTitle, {
                slots: {
                    default: 'Card Title'
                }
            })

            expect(wrapper.text()).toBe('Card Title')
        })

        it('applies custom class', () => {
            const wrapper = mount(CardTitle, {
                props: {
                    class: 'custom-title-class'
                }
            })

            expect(wrapper.attributes('class')).toContain('custom-title-class')
            expect(wrapper.attributes('class')).toContain('text-2xl font-semibold leading-none tracking-tight')
        })
    })

    // CardDescription tests
    describe('CardDescription.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(CardDescription)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.element.tagName).toBe('P')
            expect(wrapper.attributes('class')).toContain('text-sm text-muted-foreground')
        })

        it('renders slot content', () => {
            const wrapper = mount(CardDescription, {
                slots: {
                    default: 'Card Description'
                }
            })

            expect(wrapper.text()).toBe('Card Description')
        })

        it('applies custom class', () => {
            const wrapper = mount(CardDescription, {
                props: {
                    class: 'custom-description-class'
                }
            })

            expect(wrapper.attributes('class')).toContain('custom-description-class')
            expect(wrapper.attributes('class')).toContain('text-sm text-muted-foreground')
        })
    })

    // CardFooter tests
    describe('CardFooter.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(CardFooter)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.attributes('class')).toContain('flex items-center p-6 pt-0')
        })

        it('renders slot content', () => {
            const wrapper = mount(CardFooter, {
                slots: {
                    default: '<div data-testid="footer-content">Footer Content</div>'
                }
            })

            expect(wrapper.find('[data-testid="footer-content"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="footer-content"]').text()).toBe('Footer Content')
        })

        it('applies custom class', () => {
            const wrapper = mount(CardFooter, {
                props: {
                    class: 'custom-footer-class'
                }
            })

            expect(wrapper.attributes('class')).toContain('custom-footer-class')
            expect(wrapper.attributes('class')).toContain('flex items-center p-6 pt-0')
        })
    })

    // Test complete card composition
    describe('Card composition', () => {
        it('renders a complete card with all subcomponents', () => {
            const wrapper = mount({
                template: `
          <Card class="w-[350px]">
            <CardHeader>
              <CardTitle>Card Title</CardTitle>
              <CardDescription>Card Description</CardDescription>
            </CardHeader>
            <CardContent>
              <div data-testid="content">Main content goes here</div>
            </CardContent>
            <CardFooter>
              <div data-testid="footer">Footer content</div>
            </CardFooter>
          </Card>
        `,
                components: {
                    Card,
                    CardHeader,
                    CardTitle,
                    CardDescription,
                    CardContent,
                    CardFooter
                }
            })

            // Check all components are rendered
            expect(wrapper.findComponent(Card).exists()).toBe(true)
            expect(wrapper.findComponent(CardHeader).exists()).toBe(true)
            expect(wrapper.findComponent(CardTitle).exists()).toBe(true)
            expect(wrapper.findComponent(CardDescription).exists()).toBe(true)
            expect(wrapper.findComponent(CardContent).exists()).toBe(true)
            expect(wrapper.findComponent(CardFooter).exists()).toBe(true)

            // Check content
            expect(wrapper.findComponent(CardTitle).text()).toBe('Card Title')
            expect(wrapper.findComponent(CardDescription).text()).toBe('Card Description')
            expect(wrapper.find('[data-testid="content"]').text()).toBe('Main content goes here')
            expect(wrapper.find('[data-testid="footer"]').text()).toBe('Footer content')

            // Check custom class on root
            expect(wrapper.findComponent(Card).attributes('class')).toContain('w-[350px]')
        })
    })
}) 