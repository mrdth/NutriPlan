import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import ImportRecipeModal from '../ImportRecipeModal.vue';

// Mock the components
vi.mock('@/components/InputError.vue', () => ({
    default: {
        name: 'InputError',
        template: '<div class="input-error" v-if="message">{{ message }}</div>',
        props: ['message']
    }
}));

vi.mock('@/components/ui/button', () => ({
    Button: {
        name: 'Button',
        template: '<button class="button" :type="type" :disabled="disabled" @click="$emit(\'click\')"><slot /></button>',
        props: ['type', 'variant', 'disabled']
    }
}));

vi.mock('@/components/ui/dialog', () => ({
    Dialog: {
        name: 'Dialog',
        template: '<div class="dialog" v-if="open"><slot /></div>',
        props: ['open'],
        emits: ['update:open']
    },
    DialogContent: {
        name: 'DialogContent',
        template: '<div class="dialog-content"><slot /></div>',
        props: ['class']
    },
    DialogHeader: {
        name: 'DialogHeader',
        template: '<div class="dialog-header"><slot /></div>'
    },
    DialogTitle: {
        name: 'DialogTitle',
        template: '<h2 class="dialog-title"><slot /></h2>'
    },
    DialogDescription: {
        name: 'DialogDescription',
        template: '<p class="dialog-description"><slot /></p>'
    },
    DialogFooter: {
        name: 'DialogFooter',
        template: '<div class="dialog-footer"><slot /></div>'
    }
}));

vi.mock('@/components/ui/input', () => ({
    Input: {
        name: 'Input',
        template: '<input class="input" :value="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)" :type="type" :placeholder="placeholder" :required="required" />',
        props: ['modelValue', 'type', 'placeholder', 'required'],
        emits: ['update:modelValue']
    }
}));

vi.mock('@/components/ui/label', () => ({
    Label: {
        name: 'Label',
        template: '<label class="label"><slot /></label>',
        props: ['for']
    }
}));

// Create a form mock that we can modify for different tests
const formMock = {
    url: '',
    errors: {},
    processing: false,
    post: vi.fn(),
    reset: vi.fn()
};

// Mock useForm to return our mocked form
vi.mock('@inertiajs/vue3', () => ({
    Link: {
        name: 'Link',
        template: '<a :href="href" class="link"><slot /></a>',
        props: ['href']
    },
    useForm: () => formMock
}));

vi.mock('lucide-vue-next', () => ({
    Loader2Icon: {
        name: 'Loader2Icon',
        template: '<div class="loader-icon"></div>'
    }
}));

// Mock the route function
global.route = vi.fn(() => '/recipes/import');

describe('ImportRecipeModal.vue', () => {
    // Reset the form mock before each test
    beforeEach(() => {
        formMock.url = '';
        formMock.errors = {};
        formMock.processing = false;
        formMock.post = vi.fn();
        formMock.reset = vi.fn();
    });

    it('renders the modal when open prop is true', () => {
        const wrapper = mount(ImportRecipeModal, {
            props: {
                open: true
            },
            global: {
                mocks: {
                    route
                }
            }
        });

        expect(wrapper.find('.dialog').exists()).toBe(true);
        expect(wrapper.text()).toContain('Import Recipe');
        expect(wrapper.text()).toContain('Enter the URL of a recipe to import it into your collection.');
    });

    it('does not render the modal when open prop is false', () => {
        const wrapper = mount(ImportRecipeModal, {
            props: {
                open: false
            },
            global: {
                mocks: {
                    route
                }
            }
        });

        expect(wrapper.find('.dialog').exists()).toBe(false);
    });

    it('emits update:open event when cancel button is clicked', async () => {
        const wrapper = mount(ImportRecipeModal, {
            props: {
                open: true
            },
            global: {
                mocks: {
                    route
                }
            }
        });

        // Find the cancel button (it contains the text 'Cancel')
        const cancelButton = wrapper.findAll('button').find(btn => btn.text().includes('Cancel'));
        await cancelButton.trigger('click');

        // Check that the correct event was emitted
        expect(wrapper.emitted('update:open')).toBeTruthy();
        expect(wrapper.emitted('update:open')[0]).toEqual([false]);
    });

    it('has a form with URL input and submit button', () => {
        const wrapper = mount(ImportRecipeModal, {
            props: {
                open: true
            },
            global: {
                mocks: {
                    route
                }
            }
        });

        // Check for form elements
        expect(wrapper.find('form').exists()).toBe(true);
        expect(wrapper.find('input[type="url"]').exists()).toBe(true);

        // Check for submit button (contains 'Import Recipe')
        const submitButton = wrapper.findAll('button').find(btn => btn.text().includes('Import Recipe'));
        expect(submitButton).toBeTruthy();
    });

    it('disables the submit button when processing', () => {
        // Set the processing flag to true
        formMock.processing = true;

        const wrapper = mount(ImportRecipeModal, {
            props: {
                open: true
            },
            global: {
                mocks: {
                    route
                }
            }
        });

        // Find the submit button and check if it's disabled
        const submitButton = wrapper.findAll('button').find(btn => btn.text().includes('Import Recipe'));
        expect(submitButton.attributes('disabled')).toBeDefined();
    });

    it('shows the loading icon when processing', () => {
        // Set the processing flag to true
        formMock.processing = true;

        const wrapper = mount(ImportRecipeModal, {
            props: {
                open: true
            },
            global: {
                mocks: {
                    route
                }
            }
        });

        // Check for the loader icon
        expect(wrapper.find('.loader-icon').exists()).toBe(true);
    });

    it('submits the form when the submit button is clicked', async () => {
        // Mock the post method to call onSuccess
        formMock.post = vi.fn().mockImplementation((route, options) => {
            if (options && options.onSuccess) {
                options.onSuccess();
            }
        });

        const wrapper = mount(ImportRecipeModal, {
            props: {
                open: true
            },
            global: {
                mocks: {
                    route
                }
            }
        });

        // Set a URL value
        await wrapper.find('input[type="url"]').setValue('https://example.com/recipe');

        // Submit the form
        await wrapper.find('form').trigger('submit.prevent');

        // Check if post was called with the correct route
        expect(formMock.post).toHaveBeenCalledWith('/recipes/import', expect.any(Object));

        // Check if reset was called
        expect(formMock.reset).toHaveBeenCalled();

        // Check if the update:open event was emitted
        expect(wrapper.emitted('update:open')).toBeTruthy();
        expect(wrapper.emitted('update:open')[0]).toEqual([false]);
    });

    it('displays validation errors', async () => {
        // Set up an error in the form
        formMock.errors = { url: 'Please enter a valid URL' };

        const wrapper = mount(ImportRecipeModal, {
            props: {
                open: true
            },
            global: {
                mocks: {
                    route
                }
            }
        });

        // Check if the error message is displayed
        expect(wrapper.text()).toContain('Please enter a valid URL');
    });
}); 