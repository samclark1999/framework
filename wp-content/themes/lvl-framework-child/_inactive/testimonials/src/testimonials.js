document.addEventListener('DOMContentLoaded', (e) => {

    const blocks = document.querySelectorAll('.block--testimonials');

    function create_block_testimonials() {

        return {
            block: null,

            init: function (block) {
                this.block = block;

                // if (this.block.getAttribute('data-mobile') === 'slider') {
                if (this.block.querySelectorAll('.testimonial').length > 1) {
                    this.slider()
                }
                // }
            },

            slider: function () {
                const target = this.block.querySelector('.testimonials--wrapper');
                target.classList.add('swiper-wrapper');

                const wrapper = document.createElement('div');
                wrapper.classList.add('swiper');
                wrapper.classList.add('rounded');

                target.parentNode.insertBefore(wrapper, target);
                wrapper.appendChild(target);

                const sliderNavigation = this.block.querySelector('.testimonials--navigation');

                // Prev
                const prev = document.createElement('div');
                prev.classList.add('swiper-button');
                prev.classList.add('swiper-button-prev');

                sliderNavigation.appendChild(prev);

                // Pagination
                const pagination = document.createElement('div');
                pagination.classList.add('swiper-pagination');

                sliderNavigation.appendChild(pagination);

                // Next
                const next = document.createElement('div');
                next.classList.add('swiper-button');
                next.classList.add('swiper-button-next');

                sliderNavigation.appendChild(next);

                this.swiper();

            },

            swiper: function () {

                let testimonials = this.block.querySelectorAll('.testimonial');

                if (testimonials.length > 1) {

                    testimonials.forEach(testimonial => {
                        testimonial.classList.add('swiper-slide');
                    })

                    var swiper = new Swiper(this.block.querySelector('.swiper'), {
                        slidesPerView: 1,
                        spaceBetween: 30,
                        loop: true,
                        pagination: {
                            el: this.block.querySelector('.swiper-pagination'),
                            clickable: true,
                        },
                        keyboard: {
                            enabled: false,
                        },
                        a11y: {
                            enabled: true,
                        },
                        navigation: {
                            nextEl: this.block.querySelector('.swiper-button-next'),
                            prevEl: this.block.querySelector('.swiper-button-prev')
                        },
                        modules: [Navigation, Pagination, A11y]
                    });
                }
            },

            log: function (message) {
                console.log(message);
            }
        }
    }

    blocks.forEach(block => {
        const block_testimonials = create_block_testimonials();
        block_testimonials.init(block);
    });
});