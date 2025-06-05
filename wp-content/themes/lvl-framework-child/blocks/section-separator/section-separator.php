<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {

	$the_block = new Level\Block($block);
	$innerBlocks = [
			[
					'core/image',
			],
	];
	$allowedBlocks = ['core/image'];
	$inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="true" allowedBlocks="' . esc_attr(wp_json_encode($allowedBlocks)) . '" />';

	ob_start(); ?>

	<div class="block--section-separator">
		<?php
		if ($is_preview) {
//			echo $the_block->previewNotice( 'info', 'This is a section wrapper block. It is used to wrap other blocks and provide extra styling options!' );
		}

		echo $inner; ?>
	</div>

	<?php

	$output = ob_get_clean();

//    if( str_contains( $content, 'wp-block-columns' ) ) {
//        echo $the_block->renderSection($output, 'container');
//        return;
//    }

	echo $the_block->renderSection($output, false);

};
$render($block, $is_preview, $content);