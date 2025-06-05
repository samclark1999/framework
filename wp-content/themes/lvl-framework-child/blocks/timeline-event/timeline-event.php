<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {
    if (!$block) return;

    $the_block = new Level\Block($block);

    $event_date = $the_block->getField('event_date');

    $innerBlocks = [
        [
            'core/heading',
            [
                'level'   => 3,
                'content' => 'Tempore incidunt omnis quidem eius',
            ],
        ],
        [
            'core/paragraph',
            [
                'content' => 'Qui eum modi commodi optio distinctio. Impedit atque corporis ut dolores nesciunt excepturi ut sed in. Laboriosam earum atque asperiores nostrum velit minima omnis voluptas. Quia quo distinctio vel ex corrupti. Numquam et culpa. Doloribus beatae aut porro voluptatibus qui est porro.',
            ],
        ],
    ];

//    $allowedBlocks = ['core/image', 'core/heading', 'core/paragraph', 'core/button', 'core/spacer', 'core/separator'];

    $inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="false" />';

    ob_start(); ?>

    <div class="block--timeline-event" data-event-date="<?php echo $event_date; ?>">

		<?php echo $inner; ?>
        
    </div>

    <?php

    $output = ob_get_clean();

    echo $the_block->renderSection($output, false);

};

$render($block ?? false, $is_preview ?? false, $content ?? false);