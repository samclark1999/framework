<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {

	$the_block = new Level\Block($block);

	$innerBlocks = [
			[
					'lvl/tab-panel',
			],
	];

	$allowedBlocks = ['lvl/tab-panel'];

	$inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="false" allowedBlocks="' . esc_attr(wp_json_encode($allowedBlocks)) . '" />';

	ob_start();

	if($is_preview) {
		$the_block->addClass('--preview');
		echo $the_block->previewNotice('info', 'Tabs Block');
	}
	?>

	<div class="container-fluid">
		<div class="row border-top">
			<?php if (!$is_preview) { ?>
				<div class="col-auto tab-nav-col">
					<ul class="nav nav-tabs-- mt-0 ps-4 py-4 border-end h-100"></ul>
				</div>
			<?php } ?>

			<div class="col-12 col-md tab-content-col">
				<div class="tab-content p-4">
					<?php echo $inner; ?>
				</div>
			</div>
		</div>
	</div>


	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection($output, 'basic');

};

$render($block, $is_preview, $content);