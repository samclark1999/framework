<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {

    $the_block = new Level\Block($block);
	// var_dump($the_block);

    $link = $the_block->getField('link');
    $text = $link['title'] ?? 'Learn more';
    $url = $link['url'] ?? '';
    $target = ($link['target'] ?? false) ? 'target="_blank"' : 'target="_self"';

	$the_block->addAttribute(['data-lvl-stretch-link' => ($the_block->getField('link'))? "true" : "false"], 0);
	$the_block->addAttribute(['data-banner-image' => ($the_block->getField('has_banner'))? "true" : "false"], 0);

	( !$the_block->getProp('backgroundColor') )? $the_block->addAttribute(['data-bs-theme' => 'light']) : '';

    $innerBlocks = [
        [
            'core/image',
            [
                'url' => '/wp-content/themes/lvl-prime/dist/img/placeholder.webp',
            ],
        ],
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
                'content' => 'Esse error quam dolore nesciunt ut nemo quae illo fuga voluptatibus tenetur et dignissimos soluta.',
            ],
        ],
    ];

    $allowedBlocks = ['core/group', 'core/image', 'core/heading', 'core/paragraph', 'core/buttons', 'core/spacer', 'core/separator'];

    $inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="false" allowedBlocks="' . esc_attr(wp_json_encode($allowedBlocks)) . '" />';

    ob_start(); ?>

			<?php echo $inner; ?>

			<?php if ($link) : ?>
				<a href="<?php echo $url; ?>" class="visually-hidden-focusable" <?php echo $target ?> ><span class="visually-hidden"><?php echo $text; ?></span></a>
			<?php endif; ?>


    <?php

    $output = ob_get_clean();

    echo $the_block->renderSection($output, 'basic');

};

$render($block, $is_preview, $content);