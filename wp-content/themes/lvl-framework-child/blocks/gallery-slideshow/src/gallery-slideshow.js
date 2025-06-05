document.addEventListener('DOMContentLoaded', (e) => {

    const blocks = document.querySelectorAll('.block--gallery-slideshow');

    function create_block_gallery_showcase() {

        return {
            block: null,

            init: function (block) {
                this.block = block;

                if (this.block.querySelector('.gallery-inner.--slider')) {
                    this.slider();
                }
            },

            slider: function () {
                const target = this.block.querySelector('.gallery-inner.--slider .swiper-wrapper');

                if (!target) {
                    return;
                }

                const slides = target.querySelectorAll('[class*="block--"][data-slide="true"]');
                slides.forEach(slide => {
                    slide.classList.add('swiper-slide');
                });

                this.swiper();

                this.initPosition();
            },

            initPosition: function () {
                const galleryCount = this.block.querySelector('.gallery-count');
                const current = this.block.querySelector('.current');
                const total = this.block.querySelector('.total');

                if (!galleryCount || !current || !total) {
                    return;
                }

                const target = this.block.querySelector('.gallery-inner.--slider .swiper-wrapper');
                const slides = target.querySelectorAll('.swiper-slide');

                // display as at least two digits
                total.textContent = slides.length < 10 ? '0' + slides.length : slides.length;
                current.textContent = '01';
            },

            updatePosition: function (currentSlide) {
                const current = this.block.querySelector('.current');
                if(!current) return;

                current.textContent = currentSlide < 9 ? '0' + (currentSlide + 1) : currentSlide + 1;
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
                    effect: swiperWrapper.dataset.swiperEffect ? swiperWrapper.dataset.swiperEffect : 'fade',
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

                if (options.effect === 'fade') {
                    options.crossFade = true
                } else {
                    options.crossFade = false
                }

                // console.log('options:', options)
                // console.log( (options.autoplay && options.delay === "0" ? 3500 : 500))

                const swiperSlider = new Swiper(this.block.querySelector('.swiper'), {
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
                    effect: options.effect,
                    fadeEffect: {
                        crossFade: options.crossFade
                    },
                    modules: [Navigation, Pagination, Autoplay, A11y, EffectFade]
                });

                swiperSlider.on('slideChange', () => {
                    this.updatePosition(swiperSlider.realIndex);
                });
            },


            log: function (message) {
                console.log(message);
            }
        }
    }

    blocks.forEach(block => {
        const blocks = create_block_gallery_showcase();
        blocks.init(block);
    });
});