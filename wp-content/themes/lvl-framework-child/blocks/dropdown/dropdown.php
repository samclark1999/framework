<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {

	$the_block = new Level\Block($block);
	$the_block->addClass('dropdown');

	$innerBlocks = [
			// core:heading and core:list
			[
				'core/buttons',
				[
					'placeholder' => 'Dropdown',
				],
			],
			[
				'core/list',
				[
					'placeholder' => 'Dropdown List',
				],
			],
	];

	$allowedBlocks = [];

	$inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="true" allowedBlocks="' . esc_attr(wp_json_encode($allowedBlocks)) . '" />';

	ob_start(); ?>
	<?php echo $inner; ?>
	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection($output, 'basic');

};

$render($block, $is_preview, $content);