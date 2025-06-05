<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {
	if (!$block) return;

	$the_block = new LVLBlock($block);
	if ($is_preview) {
		$the_block->addClass('--preview');
	}

	$the_block->addAttribute(['data-mobile' => $the_block->getField('mobile')], 0);
	$the_block->addStyle('--card-count:' . ($the_block->getField('cards_per_row') ?: 3));

	$innerBlocks = [
		[
			'lvl/card',
		],
	];

	$allowedBlocks = ['lvl/card'];

	$inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="false" allowedBlocks="' . esc_attr(wp_json_encode($allowedBlocks)) . '" orientation="horizontal" />';

	ob_start(); ?>

	<div class="cards">

		<?php echo $inner; ?>

	</div>

<?php

	$output = ob_get_clean();

	echo $the_block->renderSection($output);
};

$render($block ?? false, $is_preview ?? false, $content ?? false);
