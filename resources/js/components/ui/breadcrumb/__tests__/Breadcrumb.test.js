import { mount } from '@vue/test-utils';
import {
    Breadcrumb,
    BreadcrumbList,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbPage,
    BreadcrumbSeparator,
    BreadcrumbEllipsis
} from '../index';

describe('Breadcrumb Components', () => {
    describe('Breadcrumb.vue', () => {
        it('renders correctly with default props', () => {
            const wrapper = mount(Breadcrumb);
            expect(wrapper.element.tagName).toBe('NAV');
            expect(wrapper.attributes('aria-label')).toBe('breadcrumb');
        });

        it('applies custom class', () => {
            const wrapper = mount(Breadcrumb, {
                props: {
                    class: 'custom-class'
                }
            });
            expect(wrapper.classes()).toContain('custom-class');
        });

        it('renders slot content', () => {
            const wrapper = mount(Breadcrumb, {
                slots: {
                    default: '<div data-test="content">Content</div>'
                }
            });
            expect(wrapper.find('[data-test="content"]').exists()).toBe(true);
        });
    });

    describe('BreadcrumbList.vue', () => {
        it('renders correctly with default props', () => {
            const wrapper = mount(BreadcrumbList);
            expect(wrapper.element.tagName).toBe('OL');
            expect(wrapper.classes()).toContain('flex');
            expect(wrapper.classes()).toContain('text-muted-foreground');
        });

        it('applies custom class', () => {
            const wrapper = mount(BreadcrumbList, {
                props: {
                    class: 'my-custom-class'
                }
            });
            expect(wrapper.classes()).toContain('my-custom-class');
        });

        it('renders slot content', () => {
            const wrapper = mount(BreadcrumbList, {
                slots: {
                    default: '<div data-test="list-content">List Content</div>'
                }
            });
            expect(wrapper.find('[data-test="list-content"]').exists()).toBe(true);
        });
    });

    describe('BreadcrumbItem.vue', () => {
        it('renders correctly with default props', () => {
            const wrapper = mount(BreadcrumbItem);
            expect(wrapper.element.tagName).toBe('LI');
            expect(wrapper.classes()).toContain('inline-flex');
        });

        it('applies custom class', () => {
            const wrapper = mount(BreadcrumbItem, {
                props: {
                    class: 'custom-item-class'
                }
            });
            expect(wrapper.classes()).toContain('custom-item-class');
        });

        it('renders slot content', () => {
            const wrapper = mount(BreadcrumbItem, {
                slots: {
                    default: '<span data-test="item-content">Item</span>'
                }
            });
            expect(wrapper.find('[data-test="item-content"]').exists()).toBe(true);
        });
    });

    describe('BreadcrumbLink.vue', () => {
        it('renders correctly with default props', () => {
            const wrapper = mount(BreadcrumbLink);
            expect(wrapper.element.tagName).toBe('A');
            expect(wrapper.classes()).toContain('transition-colors');
        });

        it('applies custom class', () => {
            const wrapper = mount(BreadcrumbLink, {
                props: {
                    class: 'my-link-class'
                }
            });
            expect(wrapper.classes()).toContain('my-link-class');
        });

        it('renders with custom element', () => {
            const wrapper = mount(BreadcrumbLink, {
                props: {
                    as: 'button'
                }
            });
            expect(wrapper.element.tagName).toBe('BUTTON');
        });

        it('renders slot content', () => {
            const wrapper = mount(BreadcrumbLink, {
                slots: {
                    default: 'Link Text'
                }
            });
            expect(wrapper.text()).toBe('Link Text');
        });
    });

    describe('BreadcrumbPage.vue', () => {
        it('renders correctly with default props', () => {
            const wrapper = mount(BreadcrumbPage);
            expect(wrapper.element.tagName).toBe('SPAN');
            expect(wrapper.attributes('role')).toBe('link');
            expect(wrapper.attributes('aria-disabled')).toBe('true');
            expect(wrapper.attributes('aria-current')).toBe('page');
            expect(wrapper.classes()).toContain('text-foreground');
        });

        it('applies custom class', () => {
            const wrapper = mount(BreadcrumbPage, {
                props: {
                    class: 'custom-page-class'
                }
            });
            expect(wrapper.classes()).toContain('custom-page-class');
        });

        it('renders slot content', () => {
            const wrapper = mount(BreadcrumbPage, {
                slots: {
                    default: 'Current Page'
                }
            });
            expect(wrapper.text()).toBe('Current Page');
        });
    });

    describe('BreadcrumbSeparator.vue', () => {
        it('renders correctly with default props', () => {
            const wrapper = mount(BreadcrumbSeparator);
            expect(wrapper.element.tagName).toBe('LI');
            expect(wrapper.attributes('role')).toBe('presentation');
            expect(wrapper.attributes('aria-hidden')).toBe('true');
            expect(wrapper.find('svg').exists()).toBe(true); // Default icon
        });

        it('applies custom class', () => {
            const wrapper = mount(BreadcrumbSeparator, {
                props: {
                    class: 'custom-separator'
                }
            });
            expect(wrapper.classes()).toContain('custom-separator');
        });

        it('renders custom separator via slot', () => {
            const wrapper = mount(BreadcrumbSeparator, {
                slots: {
                    default: '<span data-test="custom-separator">/</span>'
                }
            });
            expect(wrapper.find('[data-test="custom-separator"]').exists()).toBe(true);
            expect(wrapper.find('svg').exists()).toBe(false); // Default icon should be replaced
        });
    });

    describe('BreadcrumbEllipsis.vue', () => {
        it('renders correctly with default props', () => {
            const wrapper = mount(BreadcrumbEllipsis);
            expect(wrapper.element.tagName).toBe('SPAN');
            expect(wrapper.attributes('role')).toBe('presentation');
            expect(wrapper.attributes('aria-hidden')).toBe('true');
            expect(wrapper.find('svg').exists()).toBe(true); // Default ellipsis icon
            expect(wrapper.find('.sr-only').text()).toBe('More');
        });

        it('applies custom class', () => {
            const wrapper = mount(BreadcrumbEllipsis, {
                props: {
                    class: 'custom-ellipsis'
                }
            });
            expect(wrapper.classes()).toContain('custom-ellipsis');
        });

        it('renders custom ellipsis via slot', () => {
            const wrapper = mount(BreadcrumbEllipsis, {
                slots: {
                    default: '<span data-test="custom-ellipsis">...</span>'
                }
            });
            expect(wrapper.find('[data-test="custom-ellipsis"]').exists()).toBe(true);
            expect(wrapper.find('svg').exists()).toBe(false); // Default icon should be replaced
        });
    });

    describe('Full Breadcrumb composition', () => {
        it('renders a complete breadcrumb navigation', () => {
            const wrapper = mount({
                components: {
                    Breadcrumb,
                    BreadcrumbList,
                    BreadcrumbItem,
                    BreadcrumbLink,
                    BreadcrumbSeparator,
                    BreadcrumbPage
                },
                template: `
          <Breadcrumb>
            <BreadcrumbList>
              <BreadcrumbItem>
                <BreadcrumbLink href="/">Home</BreadcrumbLink>
              </BreadcrumbItem>
              <BreadcrumbSeparator />
              <BreadcrumbItem>
                <BreadcrumbLink href="/products">Products</BreadcrumbLink>
              </BreadcrumbItem>
              <BreadcrumbSeparator />
              <BreadcrumbItem>
                <BreadcrumbPage>Current Page</BreadcrumbPage>
              </BreadcrumbItem>
            </BreadcrumbList>
          </Breadcrumb>
        `
            });

            expect(wrapper.find('nav[aria-label="breadcrumb"]').exists()).toBe(true);
            expect(wrapper.findAll('li').length).toBe(5); // 3 items + 2 separators
            expect(wrapper.findAll('a').length).toBe(2);
            expect(wrapper.find('span[aria-current="page"]').exists()).toBe(true);
        });
    });
}); 