<?php
/**
 * Block Name: Modal
 *
 * This is the template that displays the modal block.
 */

if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {

	$the_block = new Level\Block($block);

	$type = $the_block->getField('type') ?: 'modal';

	$modal_size = $the_block->getField('modal_size') ?: 'lg';

	$offcanvas_width = $the_block->getField('offcanvas_width') ?: '50vw';
	if ($offcanvas_width === 'default' || $offcanvas_width === '') {
		$offcanvas_width = '50vw';
	}
	$the_block->addAttribute(['data-toggle-type' => $type]);

	$innerBlocks = [
			[
					'core/paragraph',
			],
	];


	$inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="false" />';

	$classes = $the_block->getClass();
	$styles = $the_block->getStylesRender();
	$attributes = $the_block->getAttribute();

	$modal_sizes = [
			'sm' => '300px',
			'md' => '500px',
			'lg' => '800px',
			'xl' => '1140px',
	];

	$preview_width = ($type === 'modal') ? $modal_sizes[$modal_size] ?? '500px' : $offcanvas_width;

	ob_start();

	if ($type === 'modal') {
		?>
		<div class="block--modal modal fade" data-bs-toggle-type="modal" id="<?php echo $the_block->getId(); ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $the_block->getId(); ?>Label" aria-hidden="true">
			<div class="modal-dialog modal-<?php echo $modal_size; ?> modal-dialog-centered-- modal-fullscreen-md-down" role="document">
				<div class="modal-content border-0<?php echo($classes ? ' ' . $classes : ' bg-white'); ?>" style="<?php echo($styles ?: ''); ?>"<?php echo($attributes ?: ' data-bs-theme="light"'); ?>>
					<div class="modal-header">
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<?php echo $inner; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	} else if ($type === 'offcanvas') {
		?>
		<div class="block--modal offcanvas offcanvas-end bg-white" style="--bs-offcanvas-width:<?php echo esc_attr($offcanvas_width); ?>;" data-bs-scroll="true" data-bs-toggle-type="offcanvas" data-bs-theme="light" id="<?php echo $the_block->getId(); ?>" tabindex="-1" aria-labelledby="<?php echo $the_block->getId(); ?>Label" aria-hidden="true">
			<div class="offcanvas-header">
				<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
			</div>
			<div class="offcanvas-body">
				<?php echo $inner; ?>
			</div>
		</div>
		<?php
	}


	$output = ob_get_clean();

	if (!$is_preview) {
		echo $the_block->renderSection($output, false);
	} else {

		?>
		<div class="m-4 p-3 border border-4 border-primary rounded" style="margin: auto; max-width: <?php echo $preview_width; ?>;transition: max-width .3s ease;" data-bs-theme="light">
			<?php

			$id = $the_block->getId() ?: '[ID NOT SET]';

			$message = 'MODAL ID is <code>' . $id . '</code> and can be triggered by linking to <code>#' . $id . '</code>. The ID can be updated under Advanced > HTML Anchor in the Block Sidebar.';
			if ($id === '[ID NOT SET]') {
				$message = 'MODAL ID is not set. Please set the ID under Advanced > HTML Anchor in the Block Sidebar.';
			}

			//            echo $the_block->previewNotice(
			//	            'warning',
			//	            $message,
			//	            'This block will inherit the light/dark mode of the parent wrapper. It is suggested to place it outside any other block in order to display as expected.'
			//            );
			echo $the_block->renderSection($inner);
			?>
		</div>
		<?php
	}


};
$render($block, $is_preview, $content);