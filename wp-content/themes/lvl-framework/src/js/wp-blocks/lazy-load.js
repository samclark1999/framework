(function (wp) {
                                    var addFilter = wp.hooks.addFilter;
                                    var createHigherOrderComponent = wp.compose.createHigherOrderComponent;
                                    var InspectorControls = wp.blockEditor.InspectorControls;
                                    var ToggleControl = wp.components.ToggleControl;
                                    var PanelBody = wp.components.PanelBody;
                                    var Fragment = wp.element.Fragment;

                                    // Add custom attribute to the image and post-featured-image blocks
                                    function addPriorityLoadAttribute(settings, name) {
                                        if (name !== 'core/image' && name !== 'core/post-featured-image') {
                                            return settings;
                                        }

                                        settings.attributes = Object.assign(settings.attributes, {
                                            priorityLoad: {
                                                type: 'boolean',
                                                default: false,
                                            },
                                        });

                                        return settings;
                                    }

                                    // Add the toggle control to the inspector controls
                                    var withPriorityLoadControl = createHigherOrderComponent(function (BlockEdit) {
                                        return function (props) {
                                            if (props.name !== 'core/image' && props.name !== 'core/post-featured-image') {
                                                return wp.element.createElement(BlockEdit, props);
                                            }

                                            var attributes = props.attributes;
                                            var setAttributes = props.setAttributes;
                                            var priorityLoad = attributes.priorityLoad;

                                            return wp.element.createElement(
                                                Fragment,
                                                null,
                                                wp.element.createElement(BlockEdit, props),
                                                wp.element.createElement(
                                                    InspectorControls,
                                                    null,
                                                    wp.element.createElement(
                                                        PanelBody,
                                                        {title: 'Priority Load Settings', initialOpen: priorityLoad},
                                                        wp.element.createElement(ToggleControl, {
                                                            label: 'Enable Priority Load',
                                                            checked: priorityLoad,
                                                            onChange: function (value) {
                                                                setAttributes({priorityLoad: value});
                                                            },
                                                        })
                                                    )
                                                )
                                            );
                                        };
                                    }, 'withPriorityLoadControl');

                                    addFilter('blocks.registerBlockType', 'my-plugin/priority-load-attribute', addPriorityLoadAttribute);
                                    addFilter('editor.BlockEdit', 'my-plugin/with-priority-load-control', withPriorityLoadControl);
                                })(window.wp);