<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {
    if (!$block) return;

    $the_block = new Level\Block($block);
    if ($is_preview) {
        $the_block->addClass('--preview');
    }

    $innerBlocks = [
        [
            'lvl/testimonial',
        ],
    ];

    $allowedBlocks = ['lvl/testimonial'];

    $inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="false" allowedBlocks="' . esc_attr(wp_json_encode($allowedBlocks)) . '" />';

    ob_start(); ?>

    <div class="slides" data-bs-theme="light">
        
        <?php echo $inner; ?>

    </div>

    <?php

    $output = ob_get_clean();

    echo $the_block->renderSection($output, true);

};

$render($block ?? false, $is_preview ?? false, $content ?? false);