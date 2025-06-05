document.addEventListener('DOMContentLoaded', (e) => {

	const blocks = document.querySelectorAll('.block--tabs');

	function create_block_tabs() {

		return {
			block: null,
			id: null,
			nav: null,

			init: function (block) {
				this.block = block;
				this.id = this.block.id;
				this.nav = this.block.querySelector('.nav-tabs--');

				this.panes();
				this.panels();
			},

			panes: function() {
				let i = 0;
				const tabs = this.block.querySelectorAll('.tab-pane');

				tabs.forEach( tab => {
					let title = tab.getAttribute('data-title');

					tab.id = this.id + '-pane-' + i;
					tab.setAttribute('aria-labeledby', '#' + this.id + '-tab-' + i);

					if( i == 0 ) {
						tab.classList.add('show', 'active');
					}

					this.tabs(title, i);

					i++;
				});
			},

			tabs: function(title, i) {
				const tab = document.createElement('li');
				tab.classList.add('nav-item');
				tab.setAttribute('role', 'presentation');

				const item = document.createElement('button');
				item.id = this.id + '-tab-' + i;
				item.classList.add('nav-link', (i == 0) ? 'active' : false);
				item.classList.add('btn');
				item.classList.add('btn-link');
				item.setAttribute('data-bs-toggle', 'tab');
				item.setAttribute('data-bs-target', '#' + this.id + '-pane-' + i);
				item.setAttribute('role', 'tab');
				item.setAttribute('aria-controls', this.id + '-pane-' + i);
				item.setAttribute('aria-selected', (i == 0) ? 'true' : false);

				const itemSpan = document.createElement('span');
				itemSpan.classList.add('tab-title');
				itemSpan.innerText = title;

				item.appendChild(itemSpan);

				// item.innerText = title;

				tab.appendChild(item);
				this.nav.appendChild(tab);
			},

			panels: function() {
				let i = 0;
				const panels = this.block.querySelectorAll('.tab-pane');

				panels.forEach( panel => {
					let heading = panel.querySelector('.accordion-header');
					let button = heading.querySelector('.accordion-button');
					let collapse = panel.querySelector('.collapse');

					heading.id = this.id + '-heading-' + i;
					heading.setAttribute('data-bs-target', '#' + this.id + '-collapse-' + i);
					heading.setAttribute('aria-controls', this.id + '-collapse-' + i);

					collapse.id = this.id + '-collapse-' + i;
					collapse.setAttribute('aria-labelledby', this.id + '-heading-' + i);
					collapse.setAttribute('data-bs-parent', '#' + this.id);

					i++;
				});
			},

			log: (message) => {
				console.log(message);
			}
		}
	}

	blocks.forEach(block => {
		const block_tabs = create_block_tabs();
		block_tabs.init(block);
	});
});