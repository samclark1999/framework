<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {
    if (!$block) return;

    $the_block = new LVLBlock($block);
    if ($is_preview) {
        $the_block->addClass('--preview');
    }

    // ACF field values
    $button_color = get_field('button_color') ?: '#000000'; 
    $pagination_color = get_field('pagination_color') ?: '#000000'; 

    $innerBlocks = [
        [
            'lvl/double-slide',
        ],
    ];

    $allowedBlocks = ['lvl/double-slide'];

    ob_start(); ?>

    <div class="block--double-slider-wrapper <?php echo $is_preview ? 'is-preview' : ''; ?>">
        <?php if ($is_preview): ?>
            <div class="preview-message">
                <h3>Double Slider</h3>
                <p>This block will display a slider of the latest resources.</p>
            </div>
        <?php else: ?>
            <style>
                .swiper-pagination-bullet {
                    background-color: <?php echo esc_attr($pagination_color); ?> !important;
                    opacity: 0.3;
                }
                .swiper-pagination-bullet-active {
                    background-color: <?php echo esc_attr($pagination_color); ?> !important;
                    opacity: 1;
                }
            </style>
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php
                    $inner_blocks = parse_blocks($content);
                    foreach ($inner_blocks as $inner_block) {
                        echo '<div class="swiper-slide">' . render_block($inner_block) . '</div>';
                    }
                    ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="swiper-button-prev" style="color: <?php echo esc_attr($button_color); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
            </div>
            <div class="swiper-button-next" style="color: <?php echo esc_attr($button_color); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </div>
        <?php endif; ?>
    </div>

    <?php
    $output = ob_get_clean();
    echo $the_block->renderSection($output, true);
};

$render($block ?? false, $is_preview ?? false, $content ?? false);
