(function (wp) {
    let registerPlugin = wp.plugins.registerPlugin;
    let PluginSidebar = wp.editor.PluginSidebar;
    let PluginSidebarMoreMenuItem = wp.editor.PluginSidebarMoreMenuItem;
    let PanelBody = wp.components.PanelBody;
    let ToggleControl = wp.components.ToggleControl;
    let withSelect = wp.data.withSelect;
    let withDispatch = wp.data.withDispatch;
    let compose = wp.compose.compose;

    // Function to update metabox order
    function updateMetaboxOrder(isChecked) {
        let metaboxContainer = document.querySelector('.interface-navigable-region.interface-interface-skeleton__content .edit-post-layout__metaboxes');
        if (metaboxContainer && metaboxContainer.parentElement.classList.contains('edit-post-meta-boxes-main')) {
            metaboxContainer = metaboxContainer.parentElement;
        }
        if (metaboxContainer) {
            const resizeToggle = metaboxContainer.querySelector('.edit-post-meta-boxes-main__presenter');

            if (isChecked) {
                metaboxContainer.style.order = '-1';
                metaboxContainer.style.height = 'auto';
                if (resizeToggle) {
                    resizeToggle.style.display = 'none';
                }
                // metaboxContainer.classList.remove('is-resizable');
            } else {
                metaboxContainer.style.order = '';
                if (resizeToggle) {
                    resizeToggle.style.display = '';
                }
                // metaboxContainer.classList.add('is-resizable');
            }
        }
    }

    // Apply the style when editor initializes
    wp.data.subscribe(function () {
        const isChecked = wp.data.select('core/edit-post').isFeatureActive('metaboxOrder');
        updateMetaboxOrder(isChecked);
    });

    let MetaboxOrderToggle = compose(
        withSelect(function (select) {
            return {
                isChecked: select('core/edit-post').isFeatureActive('metaboxOrder'),
            };
        }),
        withDispatch(function (dispatch) {
            return {
                onChange: function (isChecked) {
                    dispatch('core/edit-post').toggleFeature('metaboxOrder');
                    updateMetaboxOrder(isChecked);
                },
            };
        })
    )(function (props) {
        return wp.element.createElement(ToggleControl, {
            label: 'Show Metaboxes First',
            checked: props.isChecked,
            onChange: props.onChange,
        });
    });

    function MetaboxOrderSidebar() {
        return wp.element.createElement(
            PluginSidebar,
            {
                name: 'metabox-order-sidebar',
                title: 'Metabox Order',
            },
            wp.element.createElement(
                PanelBody,
                {},
                wp.element.createElement(MetaboxOrderToggle, {})
            )
        );
    }

    registerPlugin('metabox-order-plugin', {
        render: function () {
            return wp.element.createElement(
                wp.element.Fragment,
                {},
                wp.element.createElement(MetaboxOrderSidebar, {}),
                wp.element.createElement(PluginSidebarMoreMenuItem, {
                    target: 'metabox-order-sidebar',
                    icon: 'excerpt-view', // Updated icon
                }, 'Metabox Order')
            );
        },
        icon: 'excerpt-view' // Added plugin icon
    });
})(window.wp);