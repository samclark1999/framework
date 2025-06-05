<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {

	$the_block = new Level\Block($block);
	$the_block->addClass('py-3');

	$prefix = $the_block->getField('prefix');
	$start = $the_block->getField('count_from');
	$end = $the_block->getField('count_to');
	$suffix = $the_block->getField('suffix');
	$color = $the_block->getField('color');
	$prefix_suffix_color = $the_block->getField('prefix_suffix_color');

	$the_block->addAttribute(['data-countUp' => 'true'], 0);
	$the_block->addAttribute(['data-prefix' => $prefix], 0);
	$the_block->addAttribute(['data-suffix' => $suffix], 0);
	$the_block->addAttribute(['data-value' => $end], 0);

	// if value contains a decimal point, set the decimal value
	if (strpos($end, '.') !== false) {
		$decimal = explode('.', $end);
		$the_block->addAttribute(['data-decimal-places' => strlen($decimal[1])], 0);
	}

	if ($color) {
		$the_block->addStyle('--stat-color:' . $color, 'section');
	}
	if ($prefix_suffix_color) {
		$the_block->addStyle('--accent-color:' . $prefix_suffix_color, 'section');
	}

	ob_start(); ?>

	<div class="stat">
		<div class="value"><?php echo($is_preview ? '<span class="prefix">' . $prefix . '</span>' . $end . '<span class="suffix">' . $suffix . '</span>' : $start) ?></div>
	</div>


	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection($output, 'basic');

};

$render($block, $is_preview, $content);