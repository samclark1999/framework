import {A11y} from "swiper/modules";

document.addEventListener('DOMContentLoaded', (e) => {

    const blocks = document.querySelectorAll('.block--team');

    function create_block_team_modal() {

        return {

            block: null,
            loaded: true,
            page: 1,

            init: function (block) {

                this.block = block;

                this.modal();
            },


            modal: function () {

                const modal = document.getElementById('teamModal')
                if (modal) {
                    modal.addEventListener('show.bs.modal', event => {

                        const button = event.relatedTarget;
                        const name = button.getAttribute('data-bs-name');
                        const role = button.getAttribute('data-bs-role');
                        const bio = button.getAttribute('data-bs-bio');
                        const img = button.getAttribute('data-bs-image');

                        modal.querySelector('.avatar').src = img;
                        modal.querySelector('.modal--team-name').textContent = name;
                        modal.querySelector('.modal--team-role').textContent = role;
                        modal.querySelector('.modal--team-bio').innerHTML = bio;

                    })
                }
            },

            log: function () {
                // console.log('test');
            }
        }
    }

    function create_block_team_slider() {
        return {
            block: null,

            init: function (block) {
                this.block = block;

                if (this.block.querySelector('.team-inner.--slider')) {
                    this.slider();
                }
            },

            slider: function () {
                // const source = this.block.querySelector('.team-inner.--grid');
                // const target = this.block.querySelector('.team-inner.--slider .swiper-wrapper');
                //
                // if (!target) {
                //     return;
                // }
                //
                // if (source) {
                //     const elements = source.querySelectorAll('[class*="block--"][data-slide="true"]');
                //     elements.forEach(element => {
                //         const newElement = element.cloneNode(true);
                //         newElement.classList.add('swiper-slide');
                //         target.appendChild(newElement);
                //     });
                // } else {
                //     const slides = target.querySelectorAll('[class*="block--"][data-slide="true"]');
                //     slides.forEach(slide => {
                //         slide.classList.add('swiper-slide');
                //     });
                // }

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

                const isOverflow = this.block.classList.contains('overflow-to-edge');
                const sliderOffset = isOverflow ? 0.3 : 0;

                const swiper = new Swiper(this.block.querySelector('.swiper'), {
                    slidesPerView: 1,
                    spaceBetween: 30,
                    centeredSlides: false,
                    loop: options.loop,
                    // speed: (options.autoplay && options.delay === "0" ? 3500 : 500),
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
                            slidesPerView: 2,
                        },
                        1200: {
                            slidesPerView: (parseInt(options.slidesPerView) - sliderOffset),
                            // speed: options.speed * 100 * options.slidesPerView / (options.autoplay ? 1 : options.slidesPerView),
                            pagination: options.pagination,
                            navigation: options.navigation,
                        }
                    },
                    modules: [Navigation, Pagination, Autoplay, A11y]
                });
            },

            log: function (message) {
                console.log(message);
            }
        }
    }

    blocks.forEach(block => {
        if (block.dataset.display === 'modal') {
            const block_team_modal = create_block_team_modal();
            block_team_modal.init(block);
        }

        if (block.dataset.layout === 'slider') {
            const block_team_slider = create_block_team_slider();
            block_team_slider.init(block);
        }

        const container = block.closest('.container');
        if (container) {
            // throttle on resize
            const setContainerWidth = () => {
                block.style.setProperty('--container-width', container.offsetWidth + 'px');
            }

            const throttle = (fn, delay) => {
                let time = Date.now();
                return () => {
                    if ((time + delay - Date.now()) <= 0) {
                        fn();
                        time = Date.now();
                    }
                }
            }

            window.addEventListener('resize', throttle(setContainerWidth, 500));
            setContainerWidth();
        }
    });

    // Editor
    if (typeof wp !== 'undefined' && wp.data !== undefined) {
        const processedBlocks = new Set();

        wp.data.subscribe(function () {
            const blocks = document.querySelectorAll('.block--team');
            blocks.forEach(block => {
                if (!processedBlocks.has(block)) {
                    if (block.dataset.display === 'modal') {
                        const block_team_modal = create_block_team_modal();
                        block_team_modal.init(block);
                        processedBlocks.add(block);
                    }

                    if (block.dataset.layout === 'slider') {
                        const block_team_slider = create_block_team_slider();
                        block_team_slider.init(block);
                        processedBlocks.add(block);
                    }
                }
            });
        });
    }
});