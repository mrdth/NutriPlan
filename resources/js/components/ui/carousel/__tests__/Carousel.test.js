import { mount, flushPromises } from '@vue/test-utils'
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import Carousel from '../../carousel.vue'

describe('Carousel.vue', () => {
    let wrapper;
    const images = [
        'https://example.com/image1.jpg',
        'https://example.com/image2.jpg',
        'https://example.com/image3.jpg'
    ];

    beforeEach(() => {
        vi.useFakeTimers();
    });

    afterEach(() => {
        vi.restoreAllMocks();
        if (wrapper) {
            wrapper.unmount();
        }
    });

    it('renders the carousel with images', () => {
        wrapper = mount(Carousel, {
            props: {
                images
            }
        });

        expect(wrapper.find('.relative.w-full.h-full').exists()).toBe(true);

        // Check all images are rendered
        const imageElements = wrapper.findAll('img');
        expect(imageElements.length).toBe(images.length);
        images.forEach((image, index) => {
            expect(imageElements[index].attributes('src')).toBe(image);
            expect(imageElements[index].attributes('alt')).toBe(`Image ${index + 1}`);
        });
    });

    it('shows navigation buttons and indicators when multiple images', () => {
        wrapper = mount(Carousel, {
            props: {
                images
            }
        });

        // Navigation buttons should be visible
        expect(wrapper.find('button[aria-label="Previous image"]').exists()).toBe(true);
        expect(wrapper.find('button[aria-label="Next image"]').exists()).toBe(true);

        // Indicators should be present for each image
        const indicators = wrapper.findAll('.w-2.h-2.rounded-full');
        expect(indicators.length).toBe(images.length);
    });

    it('hides navigation when only one image is provided', () => {
        wrapper = mount(Carousel, {
            props: {
                images: [images[0]]
            }
        });

        // Navigation buttons should not be visible
        expect(wrapper.find('button[aria-label="Previous image"]').exists()).toBe(false);
        expect(wrapper.find('button[aria-label="Next image"]').exists()).toBe(false);

        // No indicators should be shown
        expect(wrapper.findAll('.w-2.h-2.rounded-full').length).toBe(0);
    });

    it('changes slide when next button is clicked', async () => {
        wrapper = mount(Carousel, {
            props: {
                images
            }
        });

        // Initially first image should be visible
        expect(wrapper.findAll('.absolute.w-full')[0].classes()).toContain('opacity-100');
        expect(wrapper.findAll('.absolute.w-full')[1].classes()).toContain('opacity-0');

        // Click next button
        await wrapper.find('button[aria-label="Next image"]').trigger('click');

        // Now second image should be visible
        expect(wrapper.findAll('.absolute.w-full')[0].classes()).toContain('opacity-0');
        expect(wrapper.findAll('.absolute.w-full')[1].classes()).toContain('opacity-100');
    });

    it('changes slide when previous button is clicked', async () => {
        wrapper = mount(Carousel, {
            props: {
                images
            }
        });

        // Go to second slide first
        await wrapper.find('button[aria-label="Next image"]').trigger('click');
        expect(wrapper.findAll('.absolute.w-full')[1].classes()).toContain('opacity-100');

        // Click previous button
        await wrapper.find('button[aria-label="Previous image"]').trigger('click');

        // Should go back to first slide
        expect(wrapper.findAll('.absolute.w-full')[0].classes()).toContain('opacity-100');
        expect(wrapper.findAll('.absolute.w-full')[1].classes()).toContain('opacity-0');
    });

    it('changes slide when indicator is clicked', async () => {
        wrapper = mount(Carousel, {
            props: {
                images
            }
        });

        // Click on third indicator (index 2)
        await wrapper.findAll('.w-2.h-2.rounded-full')[2].trigger('click');

        // Third image should be visible
        expect(wrapper.findAll('.absolute.w-full')[0].classes()).toContain('opacity-0');
        expect(wrapper.findAll('.absolute.w-full')[1].classes()).toContain('opacity-0');
        expect(wrapper.findAll('.absolute.w-full')[2].classes()).toContain('opacity-100');
    });

    it('autoplays when enabled', async () => {
        wrapper = mount(Carousel, {
            props: {
                images,
                autoplay: true,
                interval: 2000
            }
        });

        // Initially first image should be visible
        expect(wrapper.findAll('.absolute.w-full')[0].classes()).toContain('opacity-100');

        // Advance timer by interval
        vi.advanceTimersByTime(2000);
        await flushPromises();

        // Second image should now be visible
        expect(wrapper.findAll('.absolute.w-full')[0].classes()).toContain('opacity-0');
        expect(wrapper.findAll('.absolute.w-full')[1].classes()).toContain('opacity-100');

        // Advance timer again
        vi.advanceTimersByTime(2000);
        await flushPromises();

        // Third image should now be visible
        expect(wrapper.findAll('.absolute.w-full')[1].classes()).toContain('opacity-0');
        expect(wrapper.findAll('.absolute.w-full')[2].classes()).toContain('opacity-100');
    });

    it('uses default interval of 5000ms when not specified', () => {
        const setIntervalSpy = vi.spyOn(window, 'setInterval');

        wrapper = mount(Carousel, {
            props: {
                images,
                autoplay: true
            }
        });

        expect(setIntervalSpy).toHaveBeenCalledWith(expect.any(Function), 5000);
    });
}); 