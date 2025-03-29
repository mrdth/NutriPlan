import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuSeparator,
    DropdownMenuShortcut,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
    DropdownMenuTrigger,
} from '..'
import { Check, ChevronRight, Circle } from 'lucide-vue-next'

// Mock the cn utility function
vi.mock('@/lib/utils', () => ({
    cn: (...inputs) => inputs.filter(Boolean).join(' '),
}))

// Mock the Radix Vue components and hooks
vi.mock('radix-vue', () => {
    return {
        DropdownMenuRoot: {
            name: 'DropdownMenuRoot',
            template: '<div data-testid="dropdown-root"><slot /></div>',
        },
        DropdownMenuTrigger: {
            name: 'DropdownMenuTrigger',
            template: '<button data-testid="dropdown-trigger"><slot /></button>',
        },
        DropdownMenuPortal: {
            name: 'DropdownMenuPortal',
            template: '<div data-testid="dropdown-portal"><slot /></div>',
        },
        DropdownMenuContent: {
            name: 'DropdownMenuContent',
            template: '<div data-testid="dropdown-content"><slot /></div>',
        },
        DropdownMenuGroup: {
            name: 'DropdownMenuGroup',
            template: '<div data-testid="dropdown-group"><slot /></div>',
        },
        DropdownMenuItem: {
            name: 'DropdownMenuItem',
            template: '<div data-testid="dropdown-item"><slot /></div>',
        },
        DropdownMenuLabel: {
            name: 'DropdownMenuLabel',
            template: '<div data-testid="dropdown-label"><slot /></div>',
        },
        DropdownMenuSeparator: {
            name: 'DropdownMenuSeparator',
            template: '<div data-testid="dropdown-separator"></div>',
        },
        DropdownMenuSub: {
            name: 'DropdownMenuSub',
            template: '<div data-testid="dropdown-sub"><slot /></div>',
        },
        DropdownMenuSubTrigger: {
            name: 'DropdownMenuSubTrigger',
            template: '<div data-testid="dropdown-sub-trigger"><slot /></div>',
        },
        DropdownMenuSubContent: {
            name: 'DropdownMenuSubContent',
            template: '<div data-testid="dropdown-sub-content"><slot /></div>',
        },
        DropdownMenuCheckboxItem: {
            name: 'DropdownMenuCheckboxItem',
            template: '<div data-testid="dropdown-checkbox-item"><slot /></div>',
        },
        DropdownMenuRadioGroup: {
            name: 'DropdownMenuRadioGroup',
            template: '<div data-testid="dropdown-radio-group"><slot /></div>',
        },
        DropdownMenuRadioItem: {
            name: 'DropdownMenuRadioItem',
            template: '<div data-testid="dropdown-radio-item"><slot /></div>',
        },
        DropdownMenuItemIndicator: {
            name: 'DropdownMenuItemIndicator',
            template: '<div data-testid="dropdown-item-indicator"><slot /></div>',
        },
        useForwardPropsEmits: () => ({}),
        useForwardProps: () => ({}),
    }
})

// Mock the Lucide Vue icons
vi.mock('lucide-vue-next', () => ({
    Check: {
        name: 'Check',
        template: '<div data-testid="check-icon"></div>',
    },
    Circle: {
        name: 'Circle',
        template: '<div data-testid="circle-icon"></div>',
    },
    ChevronRight: {
        name: 'ChevronRight',
        template: '<div data-testid="chevron-right-icon"></div>',
    },
}))

describe('DropdownMenu Components', () => {
    // DropdownMenu tests
    describe('DropdownMenu.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenu)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-root"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DropdownMenu, {
                slots: {
                    default: '<div data-testid="dropdown-content">Dropdown Content</div>'
                }
            })

            expect(wrapper.find('[data-testid="dropdown-content"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-content"]').text()).toBe('Dropdown Content')
        })
    })

    // DropdownMenuTrigger tests
    describe('DropdownMenuTrigger.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenuTrigger)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-trigger"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DropdownMenuTrigger, {
                slots: {
                    default: 'Open Dropdown'
                }
            })

            expect(wrapper.text()).toBe('Open Dropdown')
        })

        it('has outline-none class', () => {
            const wrapper = mount(DropdownMenuTrigger)

            expect(wrapper.classes()).toContain('outline-none')
        })
    })

    // DropdownMenuContent tests
    describe('DropdownMenuContent.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenuContent)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-portal"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-content"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DropdownMenuContent, {
                slots: {
                    default: '<div data-testid="content">Content</div>'
                }
            })

            expect(wrapper.find('[data-testid="content"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="content"]').text()).toBe('Content')
        })

        it('applies custom class', () => {
            const wrapper = mount(DropdownMenuContent, {
                props: {
                    class: 'custom-content-class'
                }
            })

            expect(wrapper.find('[data-testid="dropdown-content"]').attributes('class')).toContain('custom-content-class')
        })

        it('has default sideOffset of 4', () => {
            const wrapper = mount(DropdownMenuContent)

            expect(wrapper.props()).toHaveProperty('sideOffset', 4)
        })
    })

    // DropdownMenuItem tests
    describe('DropdownMenuItem.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenuItem)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-item"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DropdownMenuItem, {
                slots: {
                    default: 'Menu Item'
                }
            })

            expect(wrapper.text()).toBe('Menu Item')
        })

        it('applies custom class', () => {
            const wrapper = mount(DropdownMenuItem, {
                props: {
                    class: 'custom-item-class'
                }
            })

            expect(wrapper.find('[data-testid="dropdown-item"]').attributes('class')).toContain('custom-item-class')
        })

        it('applies inset padding when inset prop is true', () => {
            const wrapper = mount(DropdownMenuItem, {
                props: {
                    inset: true
                }
            })

            expect(wrapper.find('[data-testid="dropdown-item"]').attributes('class')).toContain('pl-8')
        })
    })

    // DropdownMenuGroup tests
    describe('DropdownMenuGroup.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenuGroup)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-group"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DropdownMenuGroup, {
                slots: {
                    default: '<div data-testid="group-content">Group Content</div>'
                }
            })

            expect(wrapper.find('[data-testid="group-content"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="group-content"]').text()).toBe('Group Content')
        })
    })

    // DropdownMenuLabel tests
    describe('DropdownMenuLabel.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenuLabel)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-label"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DropdownMenuLabel, {
                slots: {
                    default: 'Label'
                }
            })

            expect(wrapper.text()).toBe('Label')
        })

        it('applies custom class', () => {
            const wrapper = mount(DropdownMenuLabel, {
                props: {
                    class: 'custom-label-class'
                }
            })

            expect(wrapper.find('[data-testid="dropdown-label"]').attributes('class')).toContain('custom-label-class')
        })

        it('applies inset padding when inset prop is true', () => {
            const wrapper = mount(DropdownMenuLabel, {
                props: {
                    inset: true
                }
            })

            expect(wrapper.find('[data-testid="dropdown-label"]').attributes('class')).toContain('pl-8')
        })
    })

    // DropdownMenuSeparator tests
    describe('DropdownMenuSeparator.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenuSeparator)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-separator"]').exists()).toBe(true)
        })

        it('applies custom class', () => {
            const wrapper = mount(DropdownMenuSeparator, {
                props: {
                    class: 'custom-separator-class'
                }
            })

            expect(wrapper.find('[data-testid="dropdown-separator"]').attributes('class')).toContain('custom-separator-class')
        })
    })

    // DropdownMenuShortcut tests
    describe('DropdownMenuShortcut.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenuShortcut)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.classes()).toContain('ml-auto')
            expect(wrapper.classes()).toContain('text-xs')
            expect(wrapper.classes()).toContain('tracking-widest')
            expect(wrapper.classes()).toContain('opacity-60')
        })

        it('renders slot content', () => {
            const wrapper = mount(DropdownMenuShortcut, {
                slots: {
                    default: '⌘+K'
                }
            })

            expect(wrapper.text()).toBe('⌘+K')
        })

        it('applies custom class', () => {
            const wrapper = mount(DropdownMenuShortcut, {
                props: {
                    class: 'custom-shortcut-class'
                }
            })

            expect(wrapper.classes()).toContain('custom-shortcut-class')
        })
    })

    // DropdownMenuSub tests
    describe('DropdownMenuSub.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenuSub)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-sub"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DropdownMenuSub, {
                slots: {
                    default: '<div data-testid="sub-content">Sub Content</div>'
                }
            })

            expect(wrapper.find('[data-testid="sub-content"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="sub-content"]').text()).toBe('Sub Content')
        })
    })

    // DropdownMenuSubTrigger tests
    describe('DropdownMenuSubTrigger.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenuSubTrigger)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-sub-trigger"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DropdownMenuSubTrigger, {
                slots: {
                    default: 'More Options'
                }
            })

            expect(wrapper.text()).toBe('More Options')
        })

        it('includes ChevronRight icon', () => {
            const wrapper = mount(DropdownMenuSubTrigger)

            expect(wrapper.findComponent(ChevronRight).exists()).toBe(true)
            expect(wrapper.find('[data-testid="chevron-right-icon"]').exists()).toBe(true)
        })

        it('applies custom class', () => {
            const wrapper = mount(DropdownMenuSubTrigger, {
                props: {
                    class: 'custom-trigger-class'
                }
            })

            expect(wrapper.find('[data-testid="dropdown-sub-trigger"]').attributes('class')).toContain('custom-trigger-class')
        })
    })

    // DropdownMenuSubContent tests
    describe('DropdownMenuSubContent.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenuSubContent)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-sub-content"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DropdownMenuSubContent, {
                slots: {
                    default: '<div data-testid="sub-content">Sub Content</div>'
                }
            })

            expect(wrapper.find('[data-testid="sub-content"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="sub-content"]').text()).toBe('Sub Content')
        })

        it('applies custom class', () => {
            const wrapper = mount(DropdownMenuSubContent, {
                props: {
                    class: 'custom-content-class'
                }
            })

            expect(wrapper.find('[data-testid="dropdown-sub-content"]').attributes('class')).toContain('custom-content-class')
        })
    })

    // DropdownMenuCheckboxItem tests
    describe('DropdownMenuCheckboxItem.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenuCheckboxItem)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-checkbox-item"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DropdownMenuCheckboxItem, {
                slots: {
                    default: 'Show Status Bar'
                }
            })

            expect(wrapper.text()).toBe('Show Status Bar')
        })

        it('includes Check icon in item indicator', () => {
            const wrapper = mount(DropdownMenuCheckboxItem)

            expect(wrapper.find('[data-testid="dropdown-item-indicator"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="check-icon"]').exists()).toBe(true)
        })

        it('applies custom class', () => {
            const wrapper = mount(DropdownMenuCheckboxItem, {
                props: {
                    class: 'custom-item-class'
                }
            })

            expect(wrapper.find('[data-testid="dropdown-checkbox-item"]').attributes('class')).toContain('custom-item-class')
        })
    })

    // DropdownMenuRadioGroup tests
    describe('DropdownMenuRadioGroup.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenuRadioGroup)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-radio-group"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DropdownMenuRadioGroup, {
                slots: {
                    default: '<div data-testid="radio-group-content">Radio Group Content</div>'
                }
            })

            expect(wrapper.find('[data-testid="radio-group-content"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="radio-group-content"]').text()).toBe('Radio Group Content')
        })
    })

    // DropdownMenuRadioItem tests
    describe('DropdownMenuRadioItem.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(DropdownMenuRadioItem)

            expect(wrapper.exists()).toBe(true)
            expect(wrapper.find('[data-testid="dropdown-radio-item"]').exists()).toBe(true)
        })

        it('renders slot content', () => {
            const wrapper = mount(DropdownMenuRadioItem, {
                slots: {
                    default: 'Right Align'
                }
            })

            expect(wrapper.text()).toBe('Right Align')
        })

        it('includes Circle icon in item indicator', () => {
            const wrapper = mount(DropdownMenuRadioItem)

            expect(wrapper.find('[data-testid="dropdown-item-indicator"]').exists()).toBe(true)
            expect(wrapper.find('[data-testid="circle-icon"]').exists()).toBe(true)
        })

        it('applies custom class', () => {
            const wrapper = mount(DropdownMenuRadioItem, {
                props: {
                    class: 'custom-item-class'
                }
            })

            expect(wrapper.find('[data-testid="dropdown-radio-item"]').attributes('class')).toContain('custom-item-class')
        })
    })

    // Test complete dropdown composition
    describe('DropdownMenu composition', () => {
        it('renders a complete dropdown with all subcomponents', () => {
            const wrapper = mount({
                template: `
          <DropdownMenu>
            <DropdownMenuTrigger>Open Menu</DropdownMenuTrigger>
            <DropdownMenuContent>
              <DropdownMenuLabel>My Account</DropdownMenuLabel>
              <DropdownMenuSeparator />
              <DropdownMenuGroup>
                <DropdownMenuItem>
                  Profile
                  <DropdownMenuShortcut>⇧⌘P</DropdownMenuShortcut>
                </DropdownMenuItem>
                <DropdownMenuItem>Settings</DropdownMenuItem>
              </DropdownMenuGroup>
              <DropdownMenuSeparator />
              <DropdownMenuSub>
                <DropdownMenuSubTrigger>More Options</DropdownMenuSubTrigger>
                <DropdownMenuSubContent>
                  <DropdownMenuItem>Submenu Item 1</DropdownMenuItem>
                  <DropdownMenuItem>Submenu Item 2</DropdownMenuItem>
                </DropdownMenuSubContent>
              </DropdownMenuSub>
              <DropdownMenuSeparator />
              <DropdownMenuCheckboxItem :checked="true">Autosave</DropdownMenuCheckboxItem>
              <DropdownMenuSeparator />
              <DropdownMenuRadioGroup value="right">
                <DropdownMenuLabel>Text Alignment</DropdownMenuLabel>
                <DropdownMenuRadioItem value="left">Left</DropdownMenuRadioItem>
                <DropdownMenuRadioItem value="center">Center</DropdownMenuRadioItem>
                <DropdownMenuRadioItem value="right">Right</DropdownMenuRadioItem>
              </DropdownMenuRadioGroup>
            </DropdownMenuContent>
          </DropdownMenu>
        `,
                components: {
                    DropdownMenu,
                    DropdownMenuTrigger,
                    DropdownMenuContent,
                    DropdownMenuLabel,
                    DropdownMenuSeparator,
                    DropdownMenuGroup,
                    DropdownMenuItem,
                    DropdownMenuShortcut,
                    DropdownMenuSub,
                    DropdownMenuSubTrigger,
                    DropdownMenuSubContent,
                    DropdownMenuCheckboxItem,
                    DropdownMenuRadioGroup,
                    DropdownMenuRadioItem
                }
            })

            // Check all components are rendered
            expect(wrapper.findComponent(DropdownMenu).exists()).toBe(true)
            expect(wrapper.findComponent(DropdownMenuTrigger).exists()).toBe(true)
            expect(wrapper.findComponent(DropdownMenuContent).exists()).toBe(true)
            expect(wrapper.findComponent(DropdownMenuLabel).exists()).toBe(true)
            expect(wrapper.findComponent(DropdownMenuSeparator).exists()).toBe(true)
            expect(wrapper.findComponent(DropdownMenuGroup).exists()).toBe(true)
            expect(wrapper.findComponent(DropdownMenuItem).exists()).toBe(true)
            expect(wrapper.findComponent(DropdownMenuShortcut).exists()).toBe(true)
            expect(wrapper.findComponent(DropdownMenuSub).exists()).toBe(true)
            expect(wrapper.findComponent(DropdownMenuSubTrigger).exists()).toBe(true)
            expect(wrapper.findComponent(DropdownMenuSubContent).exists()).toBe(true)
            expect(wrapper.findComponent(DropdownMenuCheckboxItem).exists()).toBe(true)
            expect(wrapper.findComponent(DropdownMenuRadioGroup).exists()).toBe(true)
            expect(wrapper.findComponent(DropdownMenuRadioItem).exists()).toBe(true)

            // Check content
            expect(wrapper.findComponent(DropdownMenuTrigger).text()).toBe('Open Menu')
            expect(wrapper.findAllComponents(DropdownMenuLabel).at(0).text()).toBe('My Account')
            expect(wrapper.findAllComponents(DropdownMenuItem).at(0).text()).toContain('Profile')
            expect(wrapper.findComponent(DropdownMenuShortcut).text()).toBe('⇧⌘P')
            expect(wrapper.findComponent(DropdownMenuSubTrigger).text()).toContain('More Options')
            expect(wrapper.findComponent(DropdownMenuCheckboxItem).text()).toBe('Autosave')
            expect(wrapper.findAllComponents(DropdownMenuRadioItem).at(2).text()).toBe('Right')
        })
    })
}) 