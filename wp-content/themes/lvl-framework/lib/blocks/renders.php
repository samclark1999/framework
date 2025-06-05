<?php if (!defined('ABSPATH')) {
	exit;
}

/**
 * Add custom class to blocks with custom block gap
 *
 * @param $block_content
 * @param $block
 *
 * @return mixed
 */
add_filter('render_block', 'lvl_add_custom_gap_class', 10, 2);
function lvl_add_custom_gap_class($block_content, $block): mixed
{
	if (!is_admin() && isset($block['attrs']['style']['spacing']['blockGap'])) {
		// Only process if there's actual content
		if (empty($block_content)) {
			return $block_content;
		}

		// Save the error reporting level and suppress warnings
		$previous_error_level = libxml_use_internal_errors(true);

		$dom = new DOMDocument();
		$dom->loadHTML('<?xml encoding="UTF-8">' . htmlspecialchars($block_content, ENT_QUOTES, 'UTF-8', false), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

		// Clear any generated errors
		libxml_clear_errors();

		// Restore previous error reporting level
		libxml_use_internal_errors($previous_error_level);

		$wrapper = $dom->getElementsByTagName('div')->item(0);

		if ($wrapper) {
			$classes = $wrapper->getAttribute('class');
			$wrapper->setAttribute('class', $classes . ' has-custom-block-gap');

			$block_content = $dom->saveHTML($wrapper);
		}
	}

	return $block_content;
}