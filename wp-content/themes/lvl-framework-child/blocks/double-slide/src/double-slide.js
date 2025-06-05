import { A11y, Autoplay, Navigation, Pagination } from 'swiper/modules';
import Swiper from 'swiper';

document.addEventListener('DOMContentLoaded', () => {
    const doubleSlides = document.querySelectorAll('.block--double-slide');

    doubleSlides.forEach(slide => {
        const blogSlider = slide.querySelector('.swiper-blog');
        const productUpdateSlider = slide.querySelector('.swiper-product-update');
        
        if (!blogSlider || !productUpdateSlider) {
            console.error('Slider container not found');
            return;
        }

        const commonSwiperConfig = {
            slidesPerView: 1,
            spaceBetween: 30,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            loop: true,
            modules: [A11y, Autoplay, Navigation, Pagination],
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            // Ensure only one slide is visible
            width: null,
        };

        const blogSwiper = new Swiper(blogSlider, {
            ...commonSwiperConfig,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });

        const productUpdateSwiper = new Swiper(productUpdateSlider, {
            ...commonSwiperConfig,
        });

        // Synchronize the sliders
        slide.querySelector('.swiper-button-next').addEventListener('click', () => {
            blogSwiper.slideNext();
            productUpdateSwiper.slideNext();
        });

        slide.querySelector('.swiper-button-prev').addEventListener('click', () => {
            blogSwiper.slidePrev();
            productUpdateSwiper.slidePrev();
        });
    });
});
