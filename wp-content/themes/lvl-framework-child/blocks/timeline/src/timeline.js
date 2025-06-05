import { Navigation, Pagination, Mousewheel, A11y } from 'swiper/modules';

document.addEventListener('DOMContentLoaded', (e) => {

	const blocks = document.querySelectorAll('.block--timeline');

	function create_block_timeline() {

		return {
			block: null,
			count: 0,


			init: function (block) {
				this.block = block;
				this.slider()
			},

			slider: function () {

				this.events();
				this.years();
				this.swiper();
				
			},

			events: function() {

				const target = this.block.querySelector('.events');
				target.classList.add('swiper');

				const wrapper = document.createElement('div');
				wrapper.classList.add('swiper-wrapper');

				target.appendChild(wrapper);

				let events = this.block.querySelectorAll('.block--timeline-event');
				events.forEach( event => {
					event.classList.add('swiper-slide');
					wrapper.appendChild(event);
				})

				this.count = events.length;

			},

			years: function() {

				const target = this.block.querySelector('.years');
				target.classList.add('swiper');

				const wrapper = document.createElement('div');
				wrapper.classList.add('swiper-wrapper');

				target.appendChild(wrapper);

				let events = this.block.querySelectorAll('.block--timeline-event');

				events.forEach( event => {

					const year = document.createElement('div');
					year.classList.add('year', 'swiper-slide');

					const date = document.createElement('span');
					date.classList.add('date');
					date.classList.add('pe-md-6');
					date.innerText = event.dataset.eventDate;
					year.appendChild(date);

					wrapper.appendChild(year);
				});
			
			},

			swiper: function() {
					let slidesPerView = 5;

					if ( this.count <= 5 ) {
						slidesPerView = this.count / 1.75;
					}

					const yearSlider = new Swiper( '#' + this.block.id + ' .years.swiper', {
						centeredSlides: true,
						centeredSlidesBounds: true,
						slidesOffsetBefore: 0,
						direction: 'horizontal',
						spaceBetween: 30,
						slidesPerView: 1,
						freeMode: false,
						slideToClickedSlide: true,
						watchSlidesVisibility: true,
						watchSlidesProgress: true,
						watchOverflow: true,
						mousewheel: {
							releaseOnEdges: true
						},
						navigation: {
							nextEl: this.block.querySelector('.swiper-button-next'),
						},
						breakpoints: {
							992: {
								centeredSlidesBounds: false,
								// slidesOffsetBefore: firstSlideHeight * -1,
								direction: 'vertical',
								slidesPerView: slidesPerView,
							}
						},
						modules: [Navigation, Pagination, Mousewheel, A11y]
					})
		
					const eventSlider = new Swiper( '#' + this.block.id + ' .events.swiper', {
						direction: 'horizontal',
						spaceBetween: 96, // 2x padding of container, see SCSS
						thumbs: {
							swiper: yearSlider,
						}
					})
		
					eventSlider.on('slideChangeTransitionStart', function () {
						yearSlider.slideTo(eventSlider.activeIndex);
					});
		
					yearSlider.on('transitionStart', function () {
						eventSlider.slideTo(yearSlider.activeIndex);
					});
			},

			log: function (message) {
				console.log(message);
			}
		}
	}

	blocks.forEach(block => {
		const block_timeline = create_block_timeline();
		block_timeline.init(block);
	});
});