<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content, $context) {

	$the_block = new Level\Block($block);
	$title = $the_block->getField('accordion_title') ?? '';
	$is_open = $the_block->getField('is_open') ?? false;
	$always_open = $context['acf/fields']['always_open'] ?? false; //$the_block->getField('always_open') ?? false;

	$innerBlocks = [
			[
					'core/heading',
					[
							'level'   => 4,
							'content' => '',
					],
			],
			[
					'core/paragraph',
					[
							'content' => '',
					],
			],
	];

//	$allowedBlocks = ['core/heading', 'core/paragraph', 'core/button', 'core/spacer', 'core/separator'];

	$inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="false" />';

//	$the_block->addAttribute(['data-bs-theme' => 'light']);

	ob_start(); ?>

<div class="block--accordion-panel accordion-item">
	<h3 class="accordion-header <?php echo $is_open ? '' : 'collapsed'; ?>" role="button" data-bs-toggle="collapse" data-bs-target="#accordion-<?php echo $block['id']; ?>" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="accordion-<?php echo $block['id']; ?>">
		<?php echo $title; ?>
	</h3>
		<div id="accordion-<?php echo $block['id']; ?>" class="accordion-collapse collapse <?php echo $is_open ? 'show' : ''; ?>" aria-labelledby="accordion-<?php echo $block['id']; ?>"<?php echo(!$always_open ? ' data-bs-parent=""' : ''); ?>>
		<div class="accordion-body">
			<?php echo $inner; ?>
		</div>
	</div>
</div>

	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection($output, 'basic');
};

$render($block, $is_preview, $content, $context);
