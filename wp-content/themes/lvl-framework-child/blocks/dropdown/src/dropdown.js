document.addEventListener('DOMContentLoaded', (e) => {
    const blocks = document.querySelectorAll('.block--dropdown');

    function create_block_dropdown() {
        return {
            block: null,
            id: null,

            init: function (block) {
                this.block = block;
                this.id = this.block.id;

                this.dropdown(block);
            },

            dropdown: function (block) {
                try {
                    const buttons = block.querySelector('.wp-block-buttons');
                    if (!buttons) return;
                    const list = block.querySelector('.wp-block-list');
                    if (!list) return;

                    // pull .wp-block-button out of .wp-block-buttons and remove .wp-block-buttons
                    const buttonWrapper = buttons.querySelector('.wp-block-button');
                    if (!buttonWrapper) return;
                    const buttonContent = buttons.querySelector('.--wp-element-button');
                    if (!buttonContent) return;

                    const button = document.createElement('button');
                    button.classList.add(...buttonWrapper.classList);
                    button.classList.add(...buttonContent.classList);
                    for (let i = 0; i < buttonContent.style.length; i++) {
                        const styleName = buttonContent.style[i];
                        button.style[styleName] = buttonContent.style[styleName];
                    }
                    button.innerHTML = buttonContent.innerHTML;

                    button.setAttribute('id', block.id + '-dropdown-button');
                    button.classList.add('dropdown-toggle');
                    button.setAttribute('type', 'button');
                    button.setAttribute('data-bs-toggle', 'dropdown');
                    button.setAttribute('aria-expanded', 'false');
                    button.setAttribute('data-bs-target', block.id + '-dropdown-list');

                    list.setAttribute('id', block.id + '-dropdown-list');
                    list.classList.add('dropdown-menu');
                    list.setAttribute('aria-labelledby', button.id);

                    //add .dropdown-item to each list item
                    const listItems = list.querySelectorAll('li');
                    listItems.forEach(item => {
                        item.classList.add('dropdown-item');
                    });

                    block.prepend(button);
                    buttons.remove();

                    new Dropdown(button);
                } catch (e) {
                    console.error(e);
                }
            },

            log: (message) => {
                console.log(message);
            }
        }
    }

    blocks.forEach(block => {
        const block_dropdown = create_block_dropdown();
        block_dropdown.init(block);
    });
});