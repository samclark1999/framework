document.addEventListener('DOMContentLoaded', (e) => {

	const blocks = document.querySelectorAll('.block--accordion');

	function create_block_accordion() {

		return {
			block: null,
			id: null,

			init: function (block) {
				this.block = block;
				this.id = this.block.id;
				this.panels();
			},

			panels: function() {
				let i = 0;
				const panels = this.block.querySelectorAll('.accordion-item');

				panels.forEach( panel => {
					let heading = panel.querySelector('.accordion-header');
					let button = heading.querySelector('.accordion-button');
					let collapse = panel.querySelector('.collapse');

					heading.id = this.id + '-heading-' + i;
					heading.setAttribute('data-bs-target', '#' + this.id + '-collapse-' + i);
					heading.setAttribute('aria-controls', this.id + '-collapse-' + i);

					collapse.id = this.id + '-collapse-' + i;
					collapse.setAttribute('aria-labelledby', this.id + '-heading-' + i);
					if(collapse.dataset.bsParent) {
					collapse.setAttribute('data-bs-parent', '#' + this.id);
					}

					i++;
				});
			},

			log: (message) => {
				console.log(message);
			}
		}
	}

	blocks.forEach(block => {
		const block_accordion = create_block_accordion();
		block_accordion.init(block);
	});
});