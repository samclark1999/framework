<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {

	$the_block = new Level\Block($block);
	$title = $the_block->getField('tab_title');

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
							'content' => 'Quo similique voluptates illo. Ut cumque quibusdam quia. Quibusdam et officiis dolorem dignissimos alias. Iusto ut voluptates sint animi deleniti sequi voluptatem corporis. Quia voluptatum fuga. Occaecati est nam ab in aspernatur molestias vero earum.',
					],
			],
	];

//	$allowedBlocks = ['core/heading', 'core/paragraph', 'core/button', 'core/spacer', 'core/separator', 'lvl/accordion'];

	$inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="false" />';

	$the_block->addAttribute(['data-bs-theme' => 'light']);

	ob_start(); ?>

	<div class="block--tab-panel tab-pane fade<?php echo($is_preview ? ' --preview' : ''); ?>" data-title="<?php echo $title; ?>" role="tabpanel">
		<div class="accordion-header collapsed" role="button" data-bs-toggle="collapse" data-bs-target="" aria-expanded="false" aria-controls="">
			<?php echo $title; ?>
		</div>
		<div class="accordion-collapse collapse" aria-labelledby="" data-bs-parent="">
			<div class="accordion-body">
				<?php echo $inner; ?>
			</div>
		</div>
	</div>

	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection($output, false);
};

$render($block, $is_preview, $content);
