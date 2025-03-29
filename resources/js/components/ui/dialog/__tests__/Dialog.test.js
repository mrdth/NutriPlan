import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '..'
import { X } from 'lucide-vue-next'

// Mock the cn utility function
vi.mock('@/lib/utils', () => ({
    cn: (...inputs) => inputs.filter(Boolean).join(' '),
}))

// Mock the Radix Vue components and hooks
vi.mock('radix-vue', () => {
    return {
        DialogRoot: {
            name: 'DialogRoot',
            template: '<div data-testid="dialog-root"><slot /></div>',
        },
        DialogTrigger: {
            name: 'DialogTrigger',
            template: '<button data-testid="dialog-trigger"><slot /></button>',
        },
        DialogPortal: {
            name: 'DialogPortal',
            template: '<div data-testid="dialog-portal"><slot /></div>',
        },
        DialogOverlay: {
            name: 'DialogOverlay',
            template: '<div data-testid="dialog-overlay"><slot /></div>',
        },
        DialogContent: {
            name: 'DialogContent',
            template: '<div data-testid="dialog-content"><slot /></div>',
        },
        DialogClose: {
            name: 'DialogClose',
            template: '<button data-testid="dialog-close"><slot /></button>',
        },
        DialogTitle: {
            name: 'DialogTitle',
            template: '<h2 data-testid="dialog-title"><slot /></h2>',
        },
        DialogDescription: {
            name: 'DialogDescription',
            template: '<p data-testid="dialog-description"><slot /></p>',
        },
        useForwardPropsEmits: () => ({}),
        useForwardProps: () => ({}),
    }
})

// Mock the Lucide Vue icons
vi.mock('lucide-vue-next', () => ({
    X: {
        name: 'X',
        template: '<div data-testid="x-icon"></div>',
    },
}))

describe('Dialog Components', () => {
    // Dialog tests
    describe('Dialog.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(Dialog)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dialog-root"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(Dialog, {
                slots: {
                    default: '<div data-testid="dialog-content">Dialog Content</div>'
                }
            })

            expect(wrapper.findAll('[data-testid="dialog-content"]').length).toBe(1)
            expect(wrapper.find('[data-testid="dialog-content"]').text()).toBe('Dialog Content')
        })
    })

    // DialogTrigger tests
    describe('DialogTrigger.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DialogTrigger)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dialog-trigger"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DialogTrigger, {
                slots: {
                    default: 'Open Dialog'
                }
            })

            expect(wrapper.text()).toBe('Open Dialog')
        })
    })

    // DialogContent tests
    describe('DialogContent.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DialogContent)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dialog-portal"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="dialog-overlay"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="dialog-content"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DialogContent, {
                slots: {
                    default: '<div data-testid="content">Content</div>'
                }
            })

            expect(wrapper.find('[data-testid="content"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="content"]').text()).toBe('Content')
        })

        it('includes close button with X icon', () => {
            const wrapper = mount(DialogContent)

            expect(wrapper.find('[data-testid="dialog-close"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="x-icon"]').exists()).toBe(true)
            expect(wrapper.find('.sr-only').text()).toBe('Close')
        })

        it('applies custom class', () => {
            const wrapper = mount(DialogContent, {
                props: {
                    class: 'custom-content-class'
                }
            })

            // Test that the class is correctly combined with default classes
            expect(wrapper.find('[data-testid="dialog-content"]').attributes('class')).toContain('custom-content-class')
        })
    })

    // DialogClose tests
    describe('DialogClose.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DialogClose)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dialog-close"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DialogClose, {
                slots: {
                    default: 'Close'
                }
            })

            expect(wrapper.text()).toBe('Close')
        })
    })

    // DialogHeader tests
    describe('DialogHeader.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DialogHeader)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.attributes('class')).toContain('flex flex-col gap-y-1.5 text-center sm:text-left')
        })

        it('renders slot content', () => {
            const wrapper = mount(DialogHeader, {
                slots: {
                    default: '<div data-testid="header-content">Header Content</div>'
                }
            })

            expect(wrapper.find('[data-testid="header-content"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="header-content"]').text()).toBe('Header Content')
        })

        it('applies custom class', () => {
            const wrapper = mount(DialogHeader, {
                props: {
                    class: 'custom-header-class'
                }
            })

            expect(wrapper.attributes('class')).toContain('custom-header-class')
            expect(wrapper.attributes('class')).toContain('flex flex-col gap-y-1.5 text-center sm:text-left')
        })
    })

    // DialogFooter tests
    describe('DialogFooter.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DialogFooter)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.attributes('class')).toContain('flex flex-col-reverse sm:flex-row sm:justify-end sm:gap-x-2')
        })

        it('renders slot content', () => {
            const wrapper = mount(DialogFooter, {
                slots: {
                    default: '<div data-testid="footer-content">Footer Content</div>'
                }
            })

            expect(wrapper.find('[data-testid="footer-content"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="footer-content"]').text()).toBe('Footer Content')
        })

        it('applies custom class', () => {
            const wrapper = mount(DialogFooter, {
                props: {
                    class: 'custom-footer-class'
                }
            })

            expect(wrapper.attributes('class')).toContain('custom-footer-class')
            expect(wrapper.attributes('class')).toContain('flex flex-col-reverse sm:flex-row sm:justify-end sm:gap-x-2')
        })
    })

    // DialogTitle tests
    describe('DialogTitle.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DialogTitle)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dialog-title"]').exists()).toBe(true)
            expect(wrapper.attributes('class')).toContain('text-lg font-semibold leading-none tracking-tight')
        })

        it('renders slot content', () => {
            const wrapper = mount(DialogTitle, {
                slots: {
                    default: 'Dialog Title'
                }
            })

            expect(wrapper.text()).toBe('Dialog Title')
        })

        it('applies custom class', () => {
            const wrapper = mount(DialogTitle, {
                props: {
                    class: 'custom-title-class'
                }
            })

            expect(wrapper.attributes('class')).toContain('custom-title-class')
            expect(wrapper.attributes('class')).toContain('text-lg font-semibold leading-none tracking-tight')
        })
    })

    // DialogDescription tests
    describe('DialogDescription.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DialogDescription)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dialog-description"]').exists()).toBe(true)
            expect(wrapper.attributes('class')).toContain('text-sm text-muted-foreground')
        })

        it('renders slot content', () => {
            const wrapper = mount(DialogDescription, {
                slots: {
                    default: 'Dialog Description'
                }
            })

            expect(wrapper.text()).toBe('Dialog Description')
        })

        it('applies custom class', () => {
            const wrapper = mount(DialogDescription, {
                props: {
                    class: 'custom-description-class'
                }
            })

            expect(wrapper.attributes('class')).toContain('custom-description-class')
            expect(wrapper.attributes('class')).toContain('text-sm text-muted-foreground')
        })
    })

    // Test complete dialog composition
    describe('Dialog composition', () => {
        it('renders a complete dialog with all subcomponents', () => {
            const wrapper = mount({
                template: `
          <Dialog>
            <DialogTrigger>Open Dialog</DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Dialog Title</DialogTitle>
                <DialogDescription>Dialog Description</DialogDescription>
              </DialogHeader>
              <div data-testid="content">Main content goes here</div>
              <DialogFooter>
                <button data-testid="cancel">Cancel</button>
                <button data-testid="confirm">Confirm</button>
              </DialogFooter>
            </DialogContent>
          </Dialog>
        `,
                components: {
                    Dialog,
                    DialogTrigger,
                    DialogContent,
                    DialogHeader,
                    DialogTitle,
                    DialogDescription,
                    DialogFooter
                }
            })

            // Check all components are rendered
            expect(wrapper.findComponent(Dialog).exists()).toBe(true)
            expect(wrapper.findComponent(DialogTrigger).exists()).toBe(true)
            expect(wrapper.findComponent(DialogContent).exists()).toBe(true)
            expect(wrapper.findComponent(DialogHeader).exists()).toBe(true)
            expect(wrapper.findComponent(DialogTitle).exists()).toBe(true)
            expect(wrapper.findComponent(DialogDescription).exists()).toBe(true)
            expect(wrapper.findComponent(DialogFooter).exists()).toBe(true)

            // Check content
            expect(wrapper.findComponent(DialogTrigger).text()).toBe('Open Dialog')
            expect(wrapper.findComponent(DialogTitle).text()).toBe('Dialog Title')
            expect(wrapper.findComponent(DialogDescription).text()).toBe('Dialog Description')
            expect(wrapper.find('[data-testid="content"]').text()).toBe('Main content goes here')
            expect(wrapper.find('[data-testid="cancel"]').text()).toBe('Cancel')
            expect(wrapper.find('[data-testid="confirm"]').text()).toBe('Confirm')
        })
    })
}) 