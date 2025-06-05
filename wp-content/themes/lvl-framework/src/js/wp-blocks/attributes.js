import {backgroundColors, getColorBySlug, isDark} from '../components/colors';

const BlockManager = {
    previousBlockAttributes: {},
    colorCache: new Map(),
    debounceTimeout: null,

    getEditorStore() {
        // Check which editor store is available
        const stores = [
            'core/block-editor',      // Post/Page editor
            'core/edit-site',         // Site editor
            'core/edit-widgets',      // Widget editor
            'core/edit-post',         // Post editor (fallback)
        ];

        for (const store of stores) {
            if (wp.data.select(store)) {
                return store;
            }
        }

        return 'core/block-editor'; // Default fallback
    },

    getSelectedBlock() {
        const store = this.getEditorStore();
        return wp.data.select(store)?.getSelectedBlock();
    },

    getBlocks() {
        const store = this.getEditorStore();
        return wp.data.select(store)?.getBlocks() || [];
    },

    updateBlockAttributes(clientId, attributes) {
        const store = this.getEditorStore();
        wp.data.dispatch(store).updateBlockAttributes(clientId, attributes);
    },

    subscribeToBlockChanges() {
        wp.data.subscribe(() => {
            clearTimeout(this.debounceTimeout);
            this.debounceTimeout = setTimeout(() => {
                const selectedBlock = this.getSelectedBlock();
                if (!selectedBlock) return;

                const {attributes: newAttributes, clientId} = selectedBlock;
                const oldAttributes = this.previousBlockAttributes[clientId];

                // Check for solid background color or gradient
                const colorSlugOrHex = newAttributes.backgroundColor || newAttributes.style?.color?.background;
                const gradientSlug = newAttributes.gradient || newAttributes.style?.color?.gradient;

                // Determine if color or gradient has changed
                const colorChanged = colorSlugOrHex !== (oldAttributes?.backgroundColor || oldAttributes?.style?.color?.background);
                const gradientChanged = gradientSlug !== (oldAttributes?.gradient || oldAttributes?.style?.color?.gradient);

                // Skip if nothing has changed
                if (oldAttributes && !colorChanged && !gradientChanged) return;

                // Determine theme based on color or gradient
                let theme = '';

                if (gradientSlug) {
                    // Extract theme from gradient name if it ends with -dark or -light
                    if (gradientSlug.endsWith('-dark')) {
                        theme = 'dark';
                    } else if (gradientSlug.endsWith('-light')) {
                        theme = 'light';
                    } else {
                        theme = isDark(gradientSlug) ? 'dark' : 'light';
                    }
                } else if (colorSlugOrHex) {
                    // Use existing color logic for solid colors
                    const backgroundColor = this.getCachedColor(colorSlugOrHex);
                    theme = backgroundColor?.color ? (this.isDarkCached(backgroundColor.color) ? 'dark' : 'light') : '';
                }




                const bs = {...newAttributes.bs, theme};

                this.updateBlockAttributes(clientId, {bs});
                this.setDataBsThemeAttribute(selectedBlock);
                this.previousBlockAttributes[clientId] = newAttributes;
            }, 100);
        });

        document.addEventListener('editorBlocksUpdated', () => {
            this.applyDataAttributes();
        });
    },

    getCachedColor(colorSlugOrHex) {
        if (!colorSlugOrHex) return null;
        const key = colorSlugOrHex.startsWith('#') ? `hex${colorSlugOrHex}` : colorSlugOrHex;
        if (!this.colorCache.has(key)) {
            const color = colorSlugOrHex.startsWith('#') ? {color: colorSlugOrHex} : getColorBySlug(backgroundColors, colorSlugOrHex);
            this.colorCache.set(key, color);
        }
        return this.colorCache.get(key);
    },

    isDarkCached(color) {
        if (!color) return false;
        if (!this.colorCache.has(color)) {
            this.colorCache.set(color, isDark(color));
        }
        return this.colorCache.get(color);
    },

    setDataBsThemeAttribute(block) {
        const {clientId, attributes} = block;
        const colorSlugOrHex = attributes.backgroundColor || attributes.style?.color?.background;
        const gradientSlug = attributes.gradient || attributes.style?.color?.gradient;

        let theme = '';

        if (gradientSlug) {
            // Extract theme from gradient name if it ends with -dark or -light
            if (gradientSlug.endsWith('-dark')) {
                theme = 'dark';
            } else if (gradientSlug.endsWith('-light')) {
                theme = 'light';
            } else {
                theme = isDark(gradientSlug) ? 'dark' : 'light';
            }
        } else if (colorSlugOrHex) {
            // Use existing color logic for solid colors
            const backgroundColor = this.getCachedColor(colorSlugOrHex);
            theme = backgroundColor?.color ? (this.isDarkCached(backgroundColor.color) ? 'dark' : 'light') : '';
        }

        const applyTheme = (blockElement) => {
            if (theme) {
                blockElement.setAttribute('data-bs-theme', theme);
            } else {
                blockElement.removeAttribute('data-bs-theme');

            }
        };

        const blockElement = document.querySelector(`[data-block="${clientId}"]`);
        if (blockElement) {
            applyTheme(blockElement);
        } else {
            const observer = new MutationObserver((mutations, obs) => {
                const blockElement = document.querySelector(`[data-block="${clientId}"]`);
                if (blockElement) {
                    applyTheme(blockElement);
                    obs.disconnect();
                }
            });
            observer.observe(document.body, {childList: true, subtree: true});
        }
    },

    traverseBlocks(blocks) {
        blocks.forEach(block => {
            this.setDataBsThemeAttribute(block);
            this.previousBlockAttributes[block.clientId] = block.attributes;
            if (block.innerBlocks?.length) this.traverseBlocks(block.innerBlocks);
        });
    },

    applyDataAttributes() {
        const blocks = this.getBlocks();
        this.traverseBlocks(blocks);
    },

    init() {
        this.subscribeToBlockChanges();

        const unsubscribe = wp.data.subscribe(() => {
            const blocks = this.getBlocks();
            if (blocks.length > 0) {
                this.applyDataAttributes();
                unsubscribe();
            }
        });
    }
};

wp.hooks.addFilter(
    'blocks.registerBlockType',
    'lvl/block-bs-attributes',
    (settings, name) => {
        return {
            ...settings,
            attributes: {
                ...settings.attributes,
                bs: {
                    type: 'object',
                    default: {}
                }
            }
        };
    }
);


BlockManager.init();
