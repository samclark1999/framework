<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {
	if (!$block) return;

	$the_block = new Level\Block($block);
	if ($is_preview) {
		$the_block->addClass('--preview');
	}

	$sticky_nav = $the_block->getField('sticky_nav');
	$nav_style = $the_block->getField('navigation_style') ?: 'pill';

	$the_block->addAttribute(['data-nav-style' => $nav_style]);

	$innerBlocks = [
			[
					'lvl/tab-pane',
			],
	];

	$allowedBlocks = ['lvl/tab-pane'];

	$inner = '<InnerBlocks 
                template="' . esc_attr(wp_json_encode($innerBlocks)) . '" 
                templateLock="false" 
                allowedBlocks="' . esc_attr(wp_json_encode($allowedBlocks)) . '" />';

	ob_start();

	if ($is_preview) {
		echo $the_block->previewNotice('info', 'Tabber Block');
	}

	?>
	<?php switch ($nav_style) {
		case 'dropdown': ?>
			<div class="nav dropdown">
				<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
					Dropdown button
				</button>
				<ul class="dropdown-menu<?php echo ($sticky_nav) ? ' sticky-top-most-lg' : ''; ?>" role="tablist">
					<?php // LINKS ADDED WITH JS
					?>
				</ul>
			</div>
			<div class="tab-content pt-4">
				<?php echo $inner; ?>
			</div>
			<?php
			break;
		case 'tabs':
			?>
			<ul class="nav nav-tabs justify-content-center<?php echo ($sticky_nav) ? ' sticky-top-most-lg' : ''; ?>" role="tablist">
				<?php // LINKS ADDED WITH JS
				?>
			</ul>
			<div class="tab-content">
				<?php echo $inner; ?>
			</div>
			<?php
			break;
		case 'pill':
		default:
			?>
			<ul class="nav nav-pills justify-content-center<?php echo ($sticky_nav) ? ' sticky-top-most-lg' : ''; ?>" role="tablist">
				<?php // LINKS ADDED WITH JS
				?>
			</ul>
			<div class="tab-content pt-4">
				<?php echo $inner; ?>
			</div>
			<?php
			break;
	}
	?>


	<?php
	if ($is_preview)
		echo '<div class="pb-5"></div>';

	$output = ob_get_clean();

	echo $the_block->renderSection($output, 'basic');

};

$render($block ?? false, $is_preview ?? false, $content ?? false);