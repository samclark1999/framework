import {Navigation, Pagination, EffectCoverflow, A11y} from 'swiper/modules';

document.addEventListener('DOMContentLoaded', (e) => {

    const blocks = document.querySelectorAll('.block--testimonials-wrapper');

    function create_block_slider_wrapper() {

        return {
            block: null,

            init: function (block) {
                this.block = block;
                this.slider()
            },

            slider: function () {
                const target = this.block.querySelector('.slides');
                const slides = this.block.querySelectorAll('.block--testimonial');

                if (slides.length < 2) {
                    target.classList.add('no-slider');
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.classList.add('swiper');

                target.parentNode.insertBefore(wrapper, target);
                wrapper.appendChild(target);

                const nav = document.createElement('div');
                nav.classList.add('swiper-nav');

                wrapper.appendChild(nav);

                slides.forEach(slide => {
                    slide.classList.add('swiper-slide');
                })

                const prev = document.createElement('div');
                prev.classList.add('swiper-button', 'swiper-button-prev');

                nav.appendChild(prev);

                prev.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 15 25" fill="none"><path d="M13.5078 1.99414L3.00781 12.4941L13.5078 22.9941" stroke="currentColor" stroke-width="3"/></svg>';

                const pagination = document.createElement('div');
                pagination.classList.add('swiper-pagination');

                nav.appendChild(pagination);

                const next = document.createElement('div');
                next.classList.add('swiper-button', 'swiper-button-next');

                next.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 15 25" fill="none"><path d="M2.01562 22.9941L12.5156 12.4941L2.01562 1.99414" stroke="currentColor" stroke-width="3"/></svg>';

                nav.appendChild(next);

                target.classList.add('swiper-wrapper');

                this.swiper();

            },

            swiper: function () {

                const swiper = new Swiper('#' + this.block.id + ' .swiper', {
                    slidesPerView: 1.1,
                    centeredSlides: true,
                    spaceBetween: 64,
                    loop: true,
                    loopAddBlankSlides: true,
                    speed: 800,
                    effect: 'coverflow',
                    coverflowEffect: {
                        rotate: 0,
                        stretch: 0,
                        depth: 100,
                        modifier: 1,
                        scale: .9,
                        slideShadows: false,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
					navigation: {
						nextEl: this.block.querySelector('.swiper-button-next'),
						prevEl: this.block.querySelector('.swiper-button-prev'),
					},
                    modules: [Navigation, Pagination, EffectCoverflow, A11y],
                    // breakpoints: {
                    //     992: {
                    //         slidesPerView: 1.3,
                    //         navigation: {
                    //             nextEl: this.block.querySelector('.swiper-button-next'),
                    //             prevEl: this.block.querySelector('.swiper-button-prev'),
                    //         },
                    //     },
                    //     1200: {
                    //         slidesPerView: 1.5,
                    //         navigation: {
                    //             nextEl: this.block.querySelector('.swiper-button-next'),
                    //             prevEl: this.block.querySelector('.swiper-button-prev'),
                    //         },
                    //     },
                    //     1440: {
                    //         slidesPerView: 1.75,
                    //         navigation: {
                    //             nextEl: this.block.querySelector('.swiper-button-next'),
                    //             prevEl: this.block.querySelector('.swiper-button-prev'),
                    //         },
                    //     }
                    // }
                });
            },

            log: function (message) {
                console.log(message);
            }
        }
    }

    blocks.forEach(block => {
        const block_slider_wrapper = create_block_slider_wrapper();
        block_slider_wrapper.init(block);
    });
});