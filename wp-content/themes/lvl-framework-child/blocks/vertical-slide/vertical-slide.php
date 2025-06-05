<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {

	$the_block = new Level\Block($block);

	$innerBlocks = [
			[
					'core/heading',
					[
							'level'   => 3,
							'content' => 'Step 1.',
					],
			],
			[
					'core/heading',
					[
							'level'   => 4,
							'content' => 'Connect with us',
					],
			],
			[
					'core/paragraph',
					[
							'content' => 'Clients let us know they have a job, job seekers let us know they\'re looking.',
					],
			],
	];

//    $allowedBlocks = ['core/columns', 'core/heading', 'core/paragraph', 'core/buttons', 'core/spacer'];

	$inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="false" />';

	ob_start(); ?>

	<div class="block--vertical-slide" <?php echo $the_block->renderAttribute(); ?> >
		<div class="vertical-slide--content">
			<?php echo $inner; ?>
		</div>
	</div>

	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection($output, false);

};

$render($block, $is_preview, $content);