<?php if (!defined('ABSPATH')) {
	exit;
}

$render = function ($block, $is_preview, $content) {

	$the_block = new Level\Block($block);

	$link = $the_block->getField('link');
	$text = $link['title'] ?? 'Learn more';
	$url = $link['url'] ?? '';
	$target = ($link['target'] ?? false) ? 'target="_blank"' : 'target="_self"';

	$innerBlocks = [
			[
					'core/image',
					[
							'url' => '/wp-content/themes/lvl-framework/dist/img/placeholder.webp',
					],
			],
	];

//	$allowedBlocks = [
//		'core/image',
//		'core/heading',
//		'core/paragraph',
//		'core/buttons',
//        'core/group',
//		'core/spacer',
//		'core/separator',
//	];

	$inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="false" />';

	if (!$the_block->getIsBgDark()) {
		if (!in_array($the_block->getBgColor(), ['transparent', '--bs-transparent'])) {
			$the_block->addAttribute(['data-bs-theme' => 'light']);
		}
	}
	$the_block->addAttribute(['data-slide' => 'true']);

	$classes = $the_block->getClass();

	ob_start(); ?>

	<div class="block--gallery-card p-3<?php echo($classes ? ' ' . $classes : ''); ?>" <?php echo $the_block->renderAttribute(); ?>>
		<div class="card-inner" data-lvl-stretch-link="true" <?php echo $the_block->renderStyle(); ?>>
			<?php echo $inner; ?>
			<?php if ($link) : ?>
				<a href="<?php echo $url; ?>" class="--card-link" <?php echo $target ?> ><span class=""><?php echo $text; ?></span></a>
			<?php endif; ?>
		</div>
	</div>

	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection($output, false);

};

$render($block ?? false, $is_preview ?? false, $content ?? false);