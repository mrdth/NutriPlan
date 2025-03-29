import { mount } from '@vue/test-utils';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger
} from '../index';
import { nextTick } from 'vue';
import fs from 'fs';
import path from 'path';

// Create stubs for Radix Vue components
const CollapsibleRootStub = {
    template: '<div><slot :open="open" /></div>',
    data() {
        return { open: false };
    },
    methods: {
        toggleOpen() {
            this.open = !this.open;
        }
    }
};

const CollapsibleContentStub = {
    template: '<div class="collapsible-content" data-state="closed"><slot /></div>',
    inheritAttrs: false
};

const CollapsibleTriggerStub = {
    template: '<button class="collapsible-trigger"><slot /></button>',
    inheritAttrs: false
};

describe('Collapsible Components', () => {
    describe('Collapsible.vue', () => {
        it('renders correctly', () => {
            const wrapper = mount(Collapsible, {
                global: {
                    stubs: {
                        CollapsibleRoot: CollapsibleRootStub
                    }
                },
                slots: {
                    default: '<div>Collapsible content</div>'
                }
            });

            expect(wrapper.exists()).toBe(true);
            expect(wrapper.text()).toContain('Collapsible content');
        });

        it('passes open state to slot', () => {
            const wrapper = mount(Collapsible, {
                global: {
                    stubs: {
                        CollapsibleRoot: CollapsibleRootStub
                    }
                },
                slots: {
                    default: '<template #default="{ open }"><div>{{ open ? "Open" : "Closed" }}</div></template>'
                }
            });

            expect(wrapper.text()).toContain('Closed');
        });

        it('forwards props to CollapsibleRoot', () => {
            const wrapper = mount(Collapsible, {
                props: {
                    defaultOpen: true,
                    disabled: true
                },
                global: {
                    stubs: {
                        CollapsibleRoot: CollapsibleRootStub
                    }
                }
            });

            expect(wrapper.attributes()).toHaveProperty('defaultopen');
            expect(wrapper.attributes()).toHaveProperty('disabled');
        });

        it('emits events from CollapsibleRoot', async () => {
            const wrapper = mount(Collapsible, {
                global: {
                    stubs: {
                        CollapsibleRoot: {
                            template: '<div><slot :open="false" /><button data-test="toggle" @click="$emit(\'update:open\', true)"></button></div>',
                            emits: ['update:open']
                        }
                    }
                }
            });

            await wrapper.find('[data-test="toggle"]').trigger('click');
            expect(wrapper.emitted()).toHaveProperty('update:open');
            expect(wrapper.emitted('update:open')?.[0]).toEqual([true]);
        });
    });

    describe('CollapsibleTrigger.vue', () => {
        it('renders correctly', () => {
            const wrapper = mount(CollapsibleTrigger, {
                global: {
                    stubs: {
                        CollapsibleTrigger: CollapsibleTriggerStub
                    }
                },
                slots: {
                    default: 'Toggle'
                }
            });

            expect(wrapper.exists()).toBe(true);
            expect(wrapper.text()).toBe('Toggle');
        });

        it('forwards props to the trigger component', () => {
            const wrapper = mount(CollapsibleTrigger, {
                props: {
                    asChild: true
                },
                global: {
                    stubs: {
                        CollapsibleTrigger: {
                            template: '<button :data-as-child="asChild" class="collapsible-trigger"><slot /></button>',
                            props: ['asChild']
                        }
                    }
                }
            });

            expect(wrapper.find('[data-as-child="true"]').exists()).toBe(true);
        });
    });

    describe('CollapsibleContent.vue', () => {
        it('renders correctly', () => {
            const wrapper = mount(CollapsibleContent, {
                global: {
                    stubs: {
                        CollapsibleContent: CollapsibleContentStub
                    }
                },
                slots: {
                    default: '<p>Collapsible content</p>'
                }
            });

            expect(wrapper.exists()).toBe(true);
            expect(wrapper.find('.collapsible-content').exists()).toBe(true);
            expect(wrapper.text()).toContain('Collapsible content');
        });

        it('contains appropriate CSS classes in template', () => {
            const componentPath = path.resolve(__dirname, '../CollapsibleContent.vue');
            const fileContent = fs.readFileSync(componentPath, 'utf8');

            expect(fileContent).toContain('overflow-hidden');
            expect(fileContent).toContain('transition-all');
            expect(fileContent).toContain('data-[state=closed]:animate-collapsible-up');
            expect(fileContent).toContain('data-[state=open]:animate-collapsible-down');
        });

        it('forwards props correctly', () => {
            const wrapper = mount(CollapsibleContent, {
                props: {
                    forceMount: true
                },
                global: {
                    stubs: {
                        CollapsibleContent: {
                            template: '<div :data-force-mount="forceMount" class="collapsible-content"><slot /></div>',
                            props: ['forceMount']
                        }
                    }
                }
            });

            expect(wrapper.find('[data-force-mount="true"]').exists()).toBe(true);
        });
    });

    describe('Full Collapsible Composition', () => {
        it('integrates all components correctly', () => {
            const wrapper = mount({
                components: {
                    Collapsible,
                    CollapsibleTrigger,
                    CollapsibleContent
                },
                template: `
                    <div>
                        <Collapsible>
                            <template #default="{ open }">
                                <CollapsibleTrigger class="trigger">{{ open ? 'Close' : 'Open' }}</CollapsibleTrigger>
                                <div class="content-wrapper">
                                    <CollapsibleContent>
                                        <div>Content</div>
                                    </CollapsibleContent>
                                </div>
                            </template>
                        </Collapsible>
                    </div>
                `,
                global: {
                    stubs: {
                        CollapsibleRoot: CollapsibleRootStub,
                        CollapsibleTrigger: CollapsibleTriggerStub,
                        CollapsibleContent: CollapsibleContentStub
                    }
                }
            });

            expect(wrapper.find('.trigger').exists()).toBe(true);
            expect(wrapper.find('.trigger').text()).toBe('Open');
            expect(wrapper.find('.content-wrapper').exists()).toBe(true);
        });
    });
}); 