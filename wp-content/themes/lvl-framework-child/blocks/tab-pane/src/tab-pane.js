document.addEventListener('DOMContentLoaded', (e) => {
    if (typeof wp !== 'undefined' && wp?.data?.subscribe) {
        const processedBlocks = new Set();
        wp.data.subscribe(() => {
            const blocks = document.querySelectorAll('.block--tab-pane');
            blocks.forEach(block => {
                if (!processedBlocks.has(block)) {
                    const btnToggleCollapse = document.querySelectorAll('.btn-toggle-collapse--pane:not(.--clickable)');
                    btnToggleCollapse.forEach(btn => {
                        btn.classList.add('--clickable');
                        btn.addEventListener('click', (e) => {
                            const target = btn.closest('.block--tab-pane');
                            target.classList.toggle('is-collapsed');
                        });
                    });
                    processedBlocks.add(block);
                }
            });
        });
    }
});