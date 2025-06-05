import {A11y} from "swiper/modules";

document.addEventListener('DOMContentLoaded', (e) => {

    const blocks = document.querySelectorAll('.block--cards');

    function create_block_cards() {

        return {
            block: null,

            init: function (block) {
                this.block = block;

                if (this.block.getAttribute('data-layout') === 'slider') {
                    this.slider('all')
                } else if (this.block.getAttribute('data-mobile') === 'slider') {
                    this.slider('mobile')
                }
            },

            slider: function (level = 'all') {
                if (level === 'all') {
                    // if not have .swiper-slide items then add class to .cards

                    const cards = this.block.querySelectorAll('.block--cards-card');
                    // if cards do not have class .swiper-slide then add class
                    cards.forEach(card => {
                        if (!card.classList.contains('swiper-slide')) {
                            card.classList.add('swiper-slide');
                        }
                    })

                    this.swiper();
                }

                if (level === 'mobile') {
                    const target = this.block.querySelector('.cards');

                    const swiper = document.createElement('div');
                    swiper.classList.add('swiper', 'd-md-none', 'p-4--');

                    const wrapper = document.createElement('div');
                    wrapper.classList.add('swiper-wrapper');

                    let cards = this.block.querySelectorAll('.block--cards-card');
                    cards.forEach(card => {
                        wrapper.appendChild(card.cloneNode(true));
                        wrapper.lastChild.classList.add('swiper-slide');
                    })

                    const pagination = document.createElement('div');
                    pagination.classList.add('swiper-pagination', 'd-md-none');

                    swiper.appendChild(wrapper);
                    target.parentNode.insertBefore(swiper, target.nextSibling);
                    target.parentNode.insertBefore(pagination, swiper.nextSibling);

                    this.swiper();
                }
            },

            swiper: function () {
                const swiperWrapper = this.block.querySelector('.swiper-wrapper');

                const options = {
                    slidesPerView: swiperWrapper.dataset.swiperSlidesPerView ? swiperWrapper.dataset.swiperSlidesPerView : 5,
                }

                var swiper = new Swiper(this.block.querySelector('.swiper'), {
                    slidesPerView: 1,
                    spaceBetween: 30,
                    centeredSlides: false,
                    loop: true,
                    a11y: {
                        enabled: true,
                    },
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
                        }
                    },
                    modules: [Navigation, Pagination, A11y]
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


window.addEventListener('load', () => {
    const blocks = document.querySelectorAll('.block--cards');

    function update_block_cards() {
        return {
            block: null,

            init: function (block) {
                this.block = block;

                // add resize event listener to window with throttling
                let resizeTimeout;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(() => {
                        this.equalizeCardHeights();
                    }, 200); // Adjust the delay as needed
                });

                // Add a slight delay to ensure all styles are applied before equalizing heights
                setTimeout(() => this.equalizeCardHeights(), 50);
            },

            equalizeCardHeights: function() {
                // Find card wrappers within this block
                const cardWrappers = this.block.querySelectorAll('.block--cards-card:has(.wp-block-spacer.flex-fill) .card-wrapper');
                let maxWrapperHeight = 0;

                // Skip if no card wrappers found
                if (cardWrappers.length === 0) return;

                // Reset all flex-fill elements to their initial state first
                const flexFillElements = this.block.querySelectorAll('.wp-block-spacer.flex-fill');
                flexFillElements.forEach(element => {
                    element.style.setProperty('--block-height', '0px');
                    element.style.minHeight = '0px';
                });

                // Wait a moment for the DOM to update after reset
                setTimeout(() => {
                    // Find tallest card-wrapper in this set
                    cardWrappers.forEach(wrapper => {
                        const wrapperHeight = wrapper.getBoundingClientRect().height;
                        if (wrapperHeight > maxWrapperHeight) {
                            maxWrapperHeight = wrapperHeight;
                        }
                    });

                    // Apply height differences to flex-fill elements
                    flexFillElements.forEach(element => {
                        const card = element.closest('.block--cards-card');
                        if (card) {
                            const cardWrapper = card.querySelector('.card-wrapper');

                            if (cardWrapper && maxWrapperHeight > 0) {
                                const currentWrapperHeight = cardWrapper.getBoundingClientRect().height;
                                const heightDifference = maxWrapperHeight - currentWrapperHeight;

                                if (heightDifference > 0) {
                                    // Apply the values (starting from 0 since we reset)
                                    element.style.setProperty('--block-height', `${heightDifference}px`);
                                    element.style.minHeight = `${heightDifference}px`;
                                }
                            }
                        }
                    });
                }, 10); // Small timeout to ensure DOM has updated after reset
            },

            log: function (message) {
                console.log(message);
            }
        }
    }

    blocks.forEach(block => {
        // const block_cards = update_block_cards();
        // block_cards.init(block);
    });
});