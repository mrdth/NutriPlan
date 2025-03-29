import { mount } from '@vue/test-utils';
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuIndicator,
    NavigationMenuList,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuTrigger,
    NavigationMenuViewport,
    navigationMenuTriggerStyle
} from '../index';
import fs from 'fs';
import path from 'path';

// Create stubs for Radix Vue components
const NavigationMenuRootStub = {
    template: '<div class="navigation-menu-root" v-bind="$attrs"><slot /></div>',
    inheritAttrs: false
};

const NavigationMenuListStub = {
    template: '<ul class="navigation-menu-list" v-bind="$attrs"><slot /></ul>',
    inheritAttrs: false
};

const NavigationMenuContentStub = {
    template: '<div class="navigation-menu-content" v-bind="$attrs"><slot /></div>',
    inheritAttrs: false
};

const NavigationMenuIndicatorStub = {
    template: '<div class="navigation-menu-indicator" v-bind="$attrs"></div>',
    inheritAttrs: false
};

const NavigationMenuViewportStub = {
    template: '<div class="navigation-menu-viewport" v-bind="$attrs"></div>',
    inheritAttrs: false
};

describe('NavigationMenu Components', () => {
    describe('NavigationMenu.vue', () => {
        it('renders correctly', () => {
            const wrapper = mount(NavigationMenu, {
                global: {
                    stubs: {
                        NavigationMenuRoot: NavigationMenuRootStub,
                        NavigationMenuViewport: NavigationMenuViewportStub
                    }
                },
                slots: {
                    default: '<div>Menu Content</div>'
                }
            });

            expect(wrapper.exists()).toBe(true);
            expect(wrapper.text()).toContain('Menu Content');
            expect(wrapper.find('.navigation-menu-root').exists()).toBe(true);
            expect(wrapper.find('.navigation-menu-viewport').exists()).toBe(true);
        });

        it('applies custom class', () => {
            const wrapper = mount(NavigationMenu, {
                props: {
                    class: 'custom-navigation-menu'
                },
                global: {
                    stubs: {
                        NavigationMenuRoot: NavigationMenuRootStub,
                        NavigationMenuViewport: NavigationMenuViewportStub
                    }
                }
            });

            expect(wrapper.attributes('class')).toContain('custom-navigation-menu');
        });

        it('forwards props', () => {
            const wrapper = mount(NavigationMenu, {
                props: {
                    value: 'test-value',
                    defaultValue: 'default-value'
                },
                global: {
                    stubs: {
                        NavigationMenuRoot: NavigationMenuRootStub,
                        NavigationMenuViewport: NavigationMenuViewportStub
                    }
                }
            });

            expect(wrapper.attributes('value')).toBe('test-value');
            expect(wrapper.attributes('defaultvalue')).toBe('default-value');
        });

        it('contains appropriate CSS classes in template', () => {
            // Read the component file directly to verify class usage
            const componentPath = path.resolve(__dirname, '../NavigationMenu.vue');
            const fileContent = fs.readFileSync(componentPath, 'utf8');

            expect(fileContent).toContain('relative z-10 flex');
            expect(fileContent).toContain('max-w-max flex-1');
            expect(fileContent).toContain('items-center justify-center');
        });
    });

    describe('NavigationMenuList.vue', () => {
        it('renders correctly', () => {
            const wrapper = mount(NavigationMenuList, {
                global: {
                    stubs: {
                        NavigationMenuList: NavigationMenuListStub
                    }
                },
                slots: {
                    default: '<li>Menu Item</li>'
                }
            });

            expect(wrapper.exists()).toBe(true);
            expect(wrapper.text()).toContain('Menu Item');
            expect(wrapper.find('.navigation-menu-list').exists()).toBe(true);
        });

        it('applies custom class', () => {
            const wrapper = mount(NavigationMenuList, {
                props: {
                    class: 'custom-menu-list'
                },
                global: {
                    stubs: {
                        NavigationMenuList: NavigationMenuListStub
                    }
                }
            });

            expect(wrapper.attributes('class')).toContain('custom-menu-list');
        });

        it('contains appropriate CSS classes in template', () => {
            const componentPath = path.resolve(__dirname, '../NavigationMenuList.vue');
            const fileContent = fs.readFileSync(componentPath, 'utf8');

            expect(fileContent).toContain('flex flex-1 list-none');
            expect(fileContent).toContain('items-center justify-center');
            expect(fileContent).toContain('gap-x-1');
        });
    });

    describe('NavigationMenuIndicator.vue', () => {
        it('renders correctly', () => {
            const wrapper = mount(NavigationMenuIndicator, {
                global: {
                    stubs: {
                        NavigationMenuIndicator: NavigationMenuIndicatorStub
                    }
                }
            });

            expect(wrapper.exists()).toBe(true);
            expect(wrapper.find('.navigation-menu-indicator').exists()).toBe(true);
        });

        it('applies custom class', () => {
            const wrapper = mount(NavigationMenuIndicator, {
                props: {
                    class: 'custom-indicator'
                },
                global: {
                    stubs: {
                        NavigationMenuIndicator: NavigationMenuIndicatorStub
                    }
                }
            });

            expect(wrapper.attributes('class')).toContain('custom-indicator');
        });

        it('contains appropriate CSS classes in template', () => {
            const componentPath = path.resolve(__dirname, '../NavigationMenuIndicator.vue');
            const fileContent = fs.readFileSync(componentPath, 'utf8');

            expect(fileContent).toContain('top-full z-[1] flex');
            expect(fileContent).toContain('data-[state=visible]:animate-in');
            expect(fileContent).toContain('data-[state=hidden]:animate-out');
        });
    });

    describe('NavigationMenuContent.vue', () => {
        it('renders correctly', () => {
            const wrapper = mount(NavigationMenuContent, {
                global: {
                    stubs: {
                        NavigationMenuContent: NavigationMenuContentStub
                    }
                },
                slots: {
                    default: '<div>Content</div>'
                }
            });

            expect(wrapper.exists()).toBe(true);
            expect(wrapper.text()).toContain('Content');
            expect(wrapper.find('.navigation-menu-content').exists()).toBe(true);
        });

        it('applies custom class', () => {
            const wrapper = mount(NavigationMenuContent, {
                props: {
                    class: 'custom-content'
                },
                global: {
                    stubs: {
                        NavigationMenuContent: NavigationMenuContentStub
                    }
                }
            });

            expect(wrapper.attributes('class')).toContain('custom-content');
        });

        it('forwards props', () => {
            const wrapper = mount(NavigationMenuContent, {
                props: {
                    forceMount: true
                },
                global: {
                    stubs: {
                        NavigationMenuContent: NavigationMenuContentStub
                    }
                }
            });

            expect(wrapper.attributes('forcemount')).toBe('true');
        });

        it('contains appropriate CSS classes in template', () => {
            const componentPath = path.resolve(__dirname, '../NavigationMenuContent.vue');
            const fileContent = fs.readFileSync(componentPath, 'utf8');

            expect(fileContent).toContain('left-0 top-0');
            expect(fileContent).toContain('data-[motion^=from-]:animate-in');
            expect(fileContent).toContain('data-[motion^=to-]:animate-out');
        });
    });

    describe('navigationMenuTriggerStyle', () => {
        it('exports a cva function', () => {
            // Check that navigationMenuTriggerStyle exports a utility class string
            expect(typeof navigationMenuTriggerStyle).toBe('function');

            // Check that calling it returns a string of CSS classes
            const classes = navigationMenuTriggerStyle();
            expect(typeof classes).toBe('string');
            expect(classes.length).toBeGreaterThan(0);
        });
    });
}); 