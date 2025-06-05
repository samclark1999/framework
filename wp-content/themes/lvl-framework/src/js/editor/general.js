// Remove default button styles
wp.domReady( () => {
    wp.blocks.unregisterBlockStyle('core/button', ['fill', 'outline']);
} );

(function(wp) {
    const addFilter = wp.hooks.addFilter;

    addFilter('editor.BlockListBlock', 'lvl/apply-default-styles', function(BlockListBlock) {
        return function(props) {
            if (!props.name.includes('lvl/')) {
                return wp.element.createElement(BlockListBlock, props);
            }

            const { attributes } = props;
            const defaultStyles = attributes?.defaultStyles || {};

            if (!attributes.style && defaultStyles) {
                wp.data.dispatch('core/block-editor').updateBlockAttributes(props.clientId, {
                    style: defaultStyles
                });
            }

            return wp.element.createElement(BlockListBlock, props);
        };
    });


    // Function to check and reload unloaded ACF blocks
    function detectAndReloadAcfBlocks() {
        // Detect unloaded blocks by the presence of placeholder elements
        const unloadedBlocks = document.querySelectorAll('.wp-block > div > .components-placeholder');

        if (unloadedBlocks.length > 0) {
            console.log(`Found ${unloadedBlocks.length} unloaded ACF blocks, attempting to reload...`);

            unloadedBlocks.forEach(placeholder => {
                // Find the block element (parent of parent)
                const blockElement = placeholder.closest('[data-block]');
                if (blockElement) {
                    const blockClientId = blockElement.getAttribute('data-block');
                    if (blockClientId) {
                        // Mark this block to avoid repeated attempts
                        placeholder.classList.add('lvl-manually-loaded');

                        // Get the block instance
                        const blockInstance = wp.data.select('core/block-editor').getBlock(blockClientId);
                        if (blockInstance) {
                            // Trigger a refresh without changing the order by adding a timestamp attribute
                            wp.data.dispatch('core/block-editor').updateBlockAttributes(
                                blockClientId,
                                { _lvlForceRefresh: Date.now() }
                            );
                            console.log(`Reloading block: ${blockClientId}`);
                        }
                    }
                }
            });

            return unloadedBlocks.length === 0;
        }

        return true; // All blocks loaded
    }

    // Initialize the progressive checker when editor is ready
    wp.domReady(() => {
        console.log('ACF block reload checker initializing...');
        let isCurrentlyReloading = false;

        // Initial delay before first check
        setTimeout(() => {
            let checkCount = 0;
            const maxChecks = 5;
            let waitTime = 15000; // Start with 15 seconds as in your code

            const progressiveCheck = () => {
                checkCount++;
                console.log(`ACF block check #${checkCount} (${waitTime/1000}s delay)`);

                isCurrentlyReloading = true;
                const allLoaded = detectAndReloadAcfBlocks();
                setTimeout(() => { isCurrentlyReloading = false; }, 1000);

                if (allLoaded) {
                    console.log('All ACF blocks loaded successfully. Stopping checks.');
                    return;
                }

                if (checkCount >= maxChecks) {
                    console.log('Maximum check attempts reached. Stopping checks.');
                    return;
                }

                // Increase wait time by 5 seconds for next check
                waitTime += 5000;
                setTimeout(progressiveCheck, waitTime);
            };

            // Start the first check
            progressiveCheck();

            // Also check when editor content changes
            let lastChangeTime = 0;
            wp.data.subscribe(() => {
                const isEditorChanged = wp.data.select('core/editor').isEditedPostDirty();
                const now = Date.now();

                // Only trigger reload if:
                // 1. Editor is dirty
                // 2. We're not currently in a reload operation
                // 3. It's been at least 10 seconds since last reload trigger
                if (isEditorChanged && !isCurrentlyReloading && (now - lastChangeTime > 10000)) {
                    lastChangeTime = now;
                    console.log('Editor content changed, scheduling block check...');
                    setTimeout(detectAndReloadAcfBlocks, 5000);
                }
            });
        }, 15000); // Initial 5 second delay
    });
})(window.wp);