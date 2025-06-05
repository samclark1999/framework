document.addEventListener('DOMContentLoaded', (e) => {

	const blocks = document.querySelectorAll('.block--vertical-slide');

	function create_block_slide() {

		return {
			block: null,

			init: function (block) {
				this.block = block;
			},

			log: (message) => {
				console.log(message);
			}
		}
	}

	blocks.forEach(block => {
		const block_slide = create_block_slide();
		block_slide.init(block);
	});
});