document.addEventListener('DOMContentLoaded', (e) => {
    const blocks = document.querySelectorAll('.block--banner-showcase');

    function create_block_cards() {

        return {
            block: null,

            init: function (block) {
                this.block = block;

                if (this.block.querySelector('.swiper')) {
                    this.slider();
                }
            },

            slider: function () {
                const target = this.block.querySelector('.swiper-wrapper');

                if (!target) {
                    return;
                }

                const elements = target.querySelectorAll('.block--banner');
                const background_images = target.querySelectorAll('.banner-background');

                if (elements.length === 1) {
                    return;
                }

                elements.forEach(element => {
                    element.classList.add('swiper-slide');
                });

                if (elements.length > 1) {
                    elements.forEach(element => {
                        element.style.display = 'flex';
                    });
                    this.swiper();

                }
            },

            swiper: function () {
                const swiperWrapper = this.block.querySelector('.swiper-wrapper');

                const options = {
                    autoplay: swiperWrapper.dataset.swiperAutoplay === 'true',
                    delay: swiperWrapper.dataset.swiperDelay ? swiperWrapper.dataset.swiperDelay : 0,
                    speed: swiperWrapper.dataset.swiperSpeed ? swiperWrapper.dataset.swiperSpeed : 5,
                    loop: swiperWrapper.dataset.swiperLoop === 'true',
                    pagination: swiperWrapper.dataset.swiperPagination === 'true',
                    navigation: swiperWrapper.dataset.swiperNavigation === 'true',
                }

                if (options.autoplay) {
                    options.autoplay = {
                        delay: (options.speed * 400) + (options.delay * 1),
                        disableOnInteraction: true,
                        pauseOnMouseEnter: true,
                        waitForTransition: false
                    }
                }

                if (options.pagination) {
                    options.pagination = {
                        el: this.block.querySelector('.swiper-pagination'),
                        clickable: true,
                    }
                }

                if (options.navigation) {
                    options.navigation = {
                        nextEl: this.block.querySelector('.swiper-button-next'),
                        prevEl: this.block.querySelector('.swiper-button-prev')
                    }
                }

                const swiper = new Swiper(this.block.querySelector('.swiper'), {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    fadeEffect: {
                        crossFade: true,
                        effect: "fade",
                    },
                    centeredSlides: false,
                    loop: options.loop,
                    speed: options.speed * 400,
                    autoplay: options.autoplay,
                    pagination: options.pagination,
                    keyboard: {
                        enabled: false,
                    },
                    a11y: {
                        enabled: true,
                    },
                    navigation: options.navigation,
                    modules: [Navigation, Pagination, Autoplay, A11y]
                });
            },

            log: function (message) {
                console.log(message);
            }
        }
    }

    blocks.forEach(block => {
        const block_cards = create_block_cards();
        block_cards.init(block);
    });
});