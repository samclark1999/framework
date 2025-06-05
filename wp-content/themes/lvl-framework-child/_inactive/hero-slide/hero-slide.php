<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {

    $the_block = new Level\Block($block);
    $image = $the_block->getField('image');

    $innerBlocks = [
        [
            'core/heading',
            [
                'level'   => 4,
                'content' => 'Tempore incidunt omnis quidem eius',
            ],
        ],
        [
            'core/paragraph',
            [
                'content' => 'Quo similique voluptates illo. Ut cumque quibusdam quia. Quibusdam et officiis dolorem dignissimos alias. Iusto ut voluptates sint animi deleniti sequi voluptatem corporis. Quia voluptatum fuga. Occaecati est nam ab in aspernatur molestias vero earum.',
            ],
        ],
    ];

    $allowedBlocks = ['core/heading', 'core/paragraph', 'core/button', 'core/spacer', 'core/separator'];

    $inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="false" allowedBlocks="' . esc_attr(wp_json_encode($allowedBlocks)) . '" />';

    ob_start(); ?>

    <div class="block--hero-slide" <?php echo $the_block->renderAttribute(); ?> >
        <div class="slide container-fluid" data-bs-theme="dark">

			<div class="content col-12 col-md-3 py-9">

				<?php echo $inner; ?>
			
			</div>

        </div>
    </div>

    <?php

    $output = ob_get_clean();

    echo $the_block->renderSection($output, false);

};

$render($block, $is_preview, $content);