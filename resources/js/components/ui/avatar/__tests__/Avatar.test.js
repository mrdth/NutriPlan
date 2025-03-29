import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import { Avatar, AvatarImage, AvatarFallback } from '../index';

// Mock the radix-vue components
vi.mock('radix-vue', () => ({
    AvatarRoot: {
        name: 'AvatarRoot',
        template: '<div class="avatar-root" :class="$attrs.class"><slot /></div>'
    },
    AvatarImage: {
        name: 'AvatarImage',
        template: '<img class="avatar-image h-full w-full object-cover" :src="src" :alt="alt" />',
        props: ['src', 'alt']
    },
    AvatarFallback: {
        name: 'AvatarFallback',
        template: '<div class="avatar-fallback"><slot /></div>',
        props: ['delayMs']
    }
}));

// Mock cn utility
vi.mock('@/lib/utils', () => ({
    cn: (...inputs) => inputs.filter(Boolean).join(' ')
}));

describe('Avatar Components', () => {
    describe('Avatar.vue', () => {
        it('renders with default props', () => {
            const wrapper = mount(Avatar);
            expect(wrapper.find('.avatar-root').exists()).toBe(true);

            // Default size is 'sm' and shape is 'circle'
            const avatarRoot = wrapper.find('.avatar-root');
            expect(avatarRoot.classes()).toContain('rounded-full');
            expect(avatarRoot.classes()).toContain('h-10');
            expect(avatarRoot.classes()).toContain('w-10');
        });

        it('applies custom class', () => {
            const wrapper = mount(Avatar, {
                props: {
                    class: 'custom-class'
                }
            });

            const avatarRoot = wrapper.find('.avatar-root');
            expect(avatarRoot.classes()).toContain('custom-class');
        });

        it('renders with size=base', () => {
            const wrapper = mount(Avatar, {
                props: {
                    size: 'base'
                }
            });

            const avatarRoot = wrapper.find('.avatar-root');
            expect(avatarRoot.classes()).toContain('h-16');
            expect(avatarRoot.classes()).toContain('w-16');
            expect(avatarRoot.classes()).toContain('text-2xl');
        });

        it('renders with size=lg', () => {
            const wrapper = mount(Avatar, {
                props: {
                    size: 'lg'
                }
            });

            const avatarRoot = wrapper.find('.avatar-root');
            expect(avatarRoot.classes()).toContain('h-32');
            expect(avatarRoot.classes()).toContain('w-32');
            expect(avatarRoot.classes()).toContain('text-5xl');
        });

        it('renders with shape=square', () => {
            const wrapper = mount(Avatar, {
                props: {
                    shape: 'square'
                }
            });

            const avatarRoot = wrapper.find('.avatar-root');
            expect(avatarRoot.classes()).toContain('rounded-md');
            expect(avatarRoot.classes()).not.toContain('rounded-full');
        });
    });

    describe('AvatarImage.vue', () => {
        it('renders an image with src and alt props', () => {
            const wrapper = mount(AvatarImage, {
                props: {
                    src: 'https://example.com/avatar.jpg',
                    alt: 'User avatar'
                }
            });

            const img = wrapper.find('img');
            expect(img.exists()).toBe(true);
            expect(img.attributes('src')).toBe('https://example.com/avatar.jpg');
            expect(img.attributes('alt')).toBe('User avatar');
            expect(img.classes()).toContain('avatar-image');
        });

        it('has the correct styling classes', () => {
            const wrapper = mount(AvatarImage, {
                props: {
                    src: 'https://example.com/avatar.jpg'
                }
            });

            const img = wrapper.find('img');
            expect(img.classes()).toContain('h-full');
            expect(img.classes()).toContain('w-full');
            expect(img.classes()).toContain('object-cover');
        });
    });

    describe('AvatarFallback.vue', () => {
        it('renders fallback content correctly', () => {
            const wrapper = mount(AvatarFallback, {
                slots: {
                    default: 'JD'
                }
            });

            expect(wrapper.find('.avatar-fallback').exists()).toBe(true);
            expect(wrapper.text()).toBe('JD');
        });

        it('passes delay prop correctly', () => {
            const wrapper = mount(AvatarFallback, {
                props: {
                    delayMs: 500
                },
                slots: {
                    default: 'JD'
                }
            });

            // In the mocked component, the delayMs prop is passed but not reflected in the DOM
            // We're mainly checking that the component doesn't error when the prop is passed
            expect(wrapper.text()).toBe('JD');
        });
    });
}); 