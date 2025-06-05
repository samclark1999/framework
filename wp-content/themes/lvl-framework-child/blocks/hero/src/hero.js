document.addEventListener('DOMContentLoaded', (e) => {
    const blocks = document.querySelectorAll('.block--hero');

    function create_block_slides() {

        return {
            block: null,

            init: function (block) {
                this.block = block;

                if (this.block.querySelector('.swiper')) {
                    this.slider();
                }

                if (this.block.classList.contains('--video-bg')) {
                    // console.log('video bg');
                    this.video();
                }
            },

            video: function () {
                let player = this.block.querySelector('.video-wrapper');
                let src = player.dataset.src;

                const types = [
                    'youtube',
                    'vimeo',
                    'wistia',
                    'vidyard',
                    'hubspot',
                    '.mp4'
                ];
                let type = types.find(t => src.includes(t));

                switch (type) {
                    case 'youtube':
                        let youtubeId = src.split('v=').pop();
                        player.innerHTML = `<iframe src="https://www.youtube-nocookie.com/embed/${youtubeId}?autoplay=1&mute=1&controls=0&loop=1&playlist=${youtubeId}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>`;
                        break;
                    case 'vimeo':
                        let vimeoId = src.replace('https://vimeo.com/', ''); // catches multi ID urls
                        player.innerHTML = `<iframe src="https://player.vimeo.com/video/${vimeoId}?autoplay=1&muted=1&loop=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`;
                        break;
                    case 'wistia':
                        let wistiaId = src.split('/').pop();
                        player.innerHTML = `<iframe src="https://fast.wistia.net/embed/iframe/${wistiaId}?autoplay=1&muted=1&loop=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`;
                        break;
                    case 'vidyard':
                        let vidyardId = src.split('/').pop();
                        player.innerHTML = `<iframe src="https://play.vidyard.com/${vidyardId}.html?v=3.1.1&type=inline&autoplay=1&muted=1&loop=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`;
                        break;
                    case 'hubspot':
                        let hubspotId = src.split('/').pop();
                        player.innerHTML = `<iframe src="https://app.hubspot.com/video/${hubspotId}?autoplay=1&muted=1&loop=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`;
                        break;
                    case '.mp4':
                        player.innerHTML = `<video aria-label="Media Player" preload="auto" autoplay muted loop><source type="video/mp4" src="${src}"/></video>`;
                        break;
                    default:
                        console.error('Invalid video type');
                }
            },


            slider: function () {
                const target = this.block.querySelector('.swiper-wrapper');

                if (!target) {
                    return;
                }

                const elements = target.querySelectorAll('.swiper-slide');

                if (elements.length === 1) {
                    return;
                }

                // elements.forEach(element => {
                //     element.classList.add('swiper-slide');
                // });

                if (elements.length > 1) {
                    // elements.forEach(element => {
                    //     element.style.display = 'flex';
                    // });
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
                    effect: "fade",
                    fadeEffect: {
                        crossFade: true,
                    },
                    allowTouchMove: false,
                    centeredSlides: false,
                    loop: true,
                    speed: options.speed * 400,
                    autoplay: true,//options.autoplay,
                    pagination: false,//options.pagination,
                    // keyboard: {
                    //     enabled: false,
                    // },
                    a11y: {
                        enabled: true,
                    },
                    navigation: false,//options.navigation,
                    modules: [Autoplay, A11y, EffectFade]
                });

                const updateAnnotation = () => {
                    const slide = swiper.slides[swiper.activeIndex];
                    const annotation = slide.querySelector('.hero-annotation');
                    const annotationDisplay = document.querySelector('.hero-annotation-display');
                    if (annotation) {
                        annotationDisplay.innerHTML = annotation.innerHTML;
                    } else {
                        annotationDisplay.innerHTML = '';
                    }
                }

                updateAnnotation();

                // swiper.on('slideChange', function () {
                // });

                swiper.on('slideChangeTransitionStart', function () {
                    const annotationDisplay = document.querySelector('.hero-annotation-display');
                    annotationDisplay.style.opacity = 0;
                });

                swiper.on('slideChangeTransitionEnd', function () {
                    updateAnnotation();

                    const annotationDisplay = document.querySelector('.hero-annotation-display');
                    annotationDisplay.style.opacity = 1;
                });
            },

            log: function (message) {
                console.log(message);
            }
        }
    }

    blocks.forEach(block => {
        const block_cards = create_block_slides();
        block_cards.init(block);
    });
});