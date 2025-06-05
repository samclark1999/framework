document.addEventListener('DOMContentLoaded', (e) => {

    const blocks = document.querySelectorAll('.block--gallery');

    function create_block_cards() {

        return {
            block: null,

            init: function (block) {
                this.block = block;

                if (this.block.querySelector('.gallery-inner.--slider')) {
                    this.slider();
                }
            },

            slider: function () {
                const source = this.block.querySelector('.gallery-inner.--grid');
                const target = this.block.querySelector('.gallery-inner.--slider .swiper-wrapper');

                if (!target) {
                    return;
                }

                if (source) {
                    const elements = source.querySelectorAll('[class*="block--"][data-slide="true"]');
                    elements.forEach(element => {
                        const newElement = element.cloneNode(true);
                        newElement.classList.add('swiper-slide');
                        target.appendChild(newElement);
                    });
                } else {
                    const slides = target.querySelectorAll('[class*="block--"][data-slide="true"]');
                    slides.forEach(slide => {
                        slide.classList.add('swiper-slide');
                    });
                }

                this.swiper();

            },

            swiper: function () {
                const swiperWrapper = this.block.querySelector('.swiper-wrapper');

                const options = {
                    slidesPerView: swiperWrapper.dataset.swiperSlidesPerView ? swiperWrapper.dataset.swiperSlidesPerView : 5,
                    autoplay: swiperWrapper.dataset.swiperAutoplay === 'true',
                    delay: swiperWrapper.dataset.swiperDelay ? swiperWrapper.dataset.swiperDelay : 0,
                    speed: swiperWrapper.dataset.swiperSpeed ? swiperWrapper.dataset.swiperSpeed : 2000,
                    loop: swiperWrapper.dataset.swiperLoop === 'true',
                    loopAddBlankSlides: swiperWrapper.dataset.swiperLoopAddBlankSlides === 'true',
                    pagination: swiperWrapper.dataset.swiperPagination === 'true',
                    navigation: swiperWrapper.dataset.swiperNavigation === 'true',
                    centered: swiperWrapper.dataset.swiperCentered === 'true',
                }

                if (options.autoplay) {
                    options.autoplay = {
                        delay: options.delay,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
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

                // console.log('options:', options)
                // console.log( (options.autoplay && options.delay === "0" ? 3500 : 500))

                const swiper = new Swiper(this.block.querySelector('.swiper'), {
                    slidesPerView: 1,
                    spaceBetween: 30,
                    centeredSlides: false,
                    loop: options.loop,
                    speed: (options.autoplay && options.delay === "0" ? 3500 : 500),
                    autoplay: options.autoplay,
                    pagination: {
                        el: this.block.querySelector('.swiper-pagination'),
                        clickable: true,
                    },
                    navigation: {
                        nextEl: this.block.querySelector('.swiper-button-next'),
                        prevEl: this.block.querySelector('.swiper-button-prev')
                    },
                    breakpoints: {
                        768: {
                            slidesPerView: options.slidesPerView,
                            speed: options.speed * 100 * options.slidesPerView / (options.autoplay ? 1 : options.slidesPerView),
                            pagination: options.pagination,
                            navigation: options.navigation,
                        }
                    },
                    modules: [Navigation, Pagination, Autoplay]
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