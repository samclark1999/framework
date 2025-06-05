<?php if (!defined('ABSPATH')) {
	exit;
}

// if you want to include the parent theme's block styles
include_once get_template_directory() . '/lib/blocks/renders.php';


// add boostrap table classes to core:table block
add_filter('render_block', 'lvl_add_table_classes', 10, 2);
function lvl_add_table_classes($block_content, $block): mixed
{
	if (!is_admin() && $block['blockName'] === 'core/table') {
		// Only process if there's actual content
		if (empty($block_content)) {
			return $block_content;
		}

		// Save the error reporting level and suppress warnings
		$previous_error_level = libxml_use_internal_errors(true);

		$dom = new DOMDocument();
		$dom->loadHTML('<?xml encoding="UTF-8">' . $block_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

		// Clear any generated errors
		libxml_clear_errors();

		// Restore previous error reporting level
		libxml_use_internal_errors($previous_error_level);

		// Get table element - it's inside a figure element
		$wrapper = $dom->getElementsByTagName('table')->item(0);

		if ($wrapper) {
			$classes = $wrapper->getAttribute('class');
			$wrapper->setAttribute('class', $classes . ' table');

			// Extract the entire modified content, not just the table
			// This preserves the surrounding figure element
			// Extract only the content we need, not the doctype or XML declaration
			$tables = $dom->getElementsByTagName('figure');
			if ($tables->length > 0) {
				$block_content = $dom->saveHTML($tables->item(0));
			} else {
				// Fallback to just saving the table if figure isn't found
				$tables = $dom->getElementsByTagName('table');
				if ($tables->length > 0) {
					$block_content = $dom->saveHTML($tables->item(0));
				}
			}
		}
	}

	return $block_content;
}