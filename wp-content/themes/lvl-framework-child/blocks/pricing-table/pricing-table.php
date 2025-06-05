<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$render = function ( $block, $is_preview, $content ) {
	$the_block = new Level\Block( $block );

// ACF JSON BACKUP FOR HIGHLIGHT COLUMN
//                {
//                    "key": "field_66a77113154c8",
//                    "label": "Highlight Column",
//                    "name": "highlight_column",
//                    "aria-label": "",
//                    "type": "true_false",
//                    "instructions": "",
//                    "required": 0,
//                    "conditional_logic": 0,
//                    "wrapper": {
//                        "width": "33%",
//                        "class": "",
//                        "id": ""
//                    },
//                    "relevanssi_exclude": 0,
//                    "message": "",
//                    "default_value": 0,
//                    "ui": 0,
//                    "ui_on_text": "",
//                    "ui_off_text": "",
//                    "parent_repeater": "field_66a76e35154c3"
//                }

	$pricing_columns     = $the_block->getField( 'pricing_columns' ) ?: [];
	$feature_items_list  = $the_block->getField( 'feature_items_list' ) ?: [];
    $pricing_table_label = $the_block->getField( 'pricing_table_label' ) ?: 'Features';

    ob_start();
    ?>
    <div class="block--pricing-table table-responsive px-1" <?php echo $the_block->renderAttribute(); ?>>
        <table class="pricing-table table table-primary">
            <thead>
            <tr class="pricing-table__header">
                <th class="pricing-table__column pricing-table__column--features">
                    <div class="pricing-table__column--features__title"><?php echo esc_html( $pricing_table_label ); ?></div>
                </th>
                <?php foreach ( $pricing_columns as $column ) : ?>
                    <th class="pricing-table__column <?php echo $column['highlight_column'] ? 'pricing-table__column--highlight' : ''; ?>">
                        <div class="pricing-table__title"><?php echo esc_html( $column['column_title'] ); ?></div>
                        <div class="pricing-table__price"><?php echo esc_html( $column['price'] ); ?></div>
                        <a href="<?php echo esc_url( $column['button_link'] ); ?>" class="btn btn-secondary lh-1 --pricing-table__button"><?php echo esc_html( $column['button_text'] ); ?></a>
                    </th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody class="pricing-table__features">
                <?php foreach ( $feature_items_list as $feature ) : ?>
                    <tr class="pricing-table__feature-row">
                        <td class="pricing-table__feature-text"><?php echo esc_html( $feature['item_text'] ); ?></td>
                        <?php foreach ( $feature['column_content'] as $content ) : ?>
                            <td class="pricing-table__feature-cell">
                                <?php if ( $content['content_type'] === 'Checkbox' ) : ?>
                                    <svg class="pricing-table__checkmark" xmlns="http://www.w3.org/2000/svg" width="35" height="24" viewBox="0 0 35 24" fill="none">
                                        <g clip-path="url(#clip0_224_3786)">
                                            <path d="M12.6563 23.6257L0.944415 11.9011C0.518528 11.4773 0.518528 10.771 0.944415 10.3473L2.506 8.79341C2.93189 8.36963 3.6417 8.36963 4.06759 8.79341L12.8693 17.6221C13.1248 17.9117 13.5791 17.94 13.8701 17.6857C13.8914 17.6645 13.9127 17.6433 13.934 17.6221L31.1824 0.317834C31.6083 -0.105945 32.3181 -0.105945 32.744 0.317834L34.3056 1.87169C34.7315 2.29547 34.7315 3.00177 34.3056 3.42554L14.2179 23.6257C13.792 24.1201 13.0822 24.1201 12.6563 23.6257Z" fill="#08304F"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_224_3786">
                                                <rect width="34" height="24" fill="white" transform="translate(0.625)"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                <?php else : ?>
                                    <?php echo esc_html( $content['custom_text'] ); ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    $output = ob_get_clean();
    echo $the_block->renderSection( $output );
};

$render( $block, $is_preview, $content );