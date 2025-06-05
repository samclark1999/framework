import { Navigation, Pagination, EffectFade, A11y, Autoplay } from 'swiper/modules';

document.addEventListener('DOMContentLoaded', (e) => {

	const blocks = document.querySelectorAll('.block--hero-slider-wrapper');

	function create_block_hero_slider_wrapper() {

		return {
			block: null,

			init: function (block) {
				this.block = block;
				this.slider()
			},

			slider: function () {
				const target = this.block.querySelector('.slides');

				const wrapper = document.createElement('div');
				wrapper.classList.add('swiper');

				target.parentNode.insertBefore(wrapper, target);
				wrapper.appendChild(target);

				let slides = this.block.querySelectorAll('.block--hero-slide');
				slides.forEach( slide => {
					slide.classList.add('swiper-slide');
				})

				const prev = document.createElement('div');
				prev.classList.add('swiper-button','swiper-button-prev');
				prev.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="25" viewBox="0 0 15 25" fill="none"><path d="M13.5078 1.99414L3.00781 12.4941L13.5078 22.9941" stroke="currentColor" stroke-width="3"/></svg>';
				
				wrapper.appendChild(prev);

				// const pagination = document.createElement('div');
				// pagination.classList.add('swiper-pagination');

				// wrapper.appendChild(pagination);

				const next = document.createElement('div');
				next.classList.add('swiper-button','swiper-button-next');
				next.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="25" viewBox="0 0 15 25" fill="none"><path d="M2.01562 22.9941L12.5156 12.4941L2.01562 1.99414" stroke="currentColor" stroke-width="3"/></svg>';
				
				wrapper.appendChild(next);

				target.classList.add('swiper-wrapper');

				this.swiper();
				
			},

			swiper: function() {

					const swiper = new Swiper('#' + this.block.id + ' .swiper', {
						slidesPerView: 1,
						spaceBetween: 0,
						autoplay: {
							delay: 5000,
							pauseOnMouseEnter: true,
						},
						loop: true,
						effect: 'fade',
						fadeEffect: {
							crossFade: true
						},
						speed: 800,
						pagination: {
							el: this.block.querySelector('.swiper-pagination'),
							clickable: true,
						},
						navigation: {
							nextEl: this.block.querySelector('.swiper-button-next'),
							prevEl: this.block.querySelector('.swiper-button-prev'),
						},
						modules: [Navigation, Pagination, EffectFade, A11y, Autoplay]
					});
			},

			log: function (message) {
				console.log(message);
			}
		}
	}

	blocks.forEach(block => {
		const block_hero_slider_wrapper = create_block_hero_slider_wrapper();
		block_hero_slider_wrapper.init(block);
	});
});