<?php

/**
 * @param $block_content
 * @param $block
 *
 * @return false|mixed|string
 */
function lvl_render_block_filter__columns($block_content, $block): mixed
{

	$block['blockName'] = ($block['blockName'] ?? 'noname');

	if ('core/columns' === $block['blockName']) {
		$theme_json_settings = wp_get_global_settings();

		if (($theme_json_settings['spacing']['blockGap'] ?? null) !== null) {
			return $block_content;
		}
		if (!isset($block['attrs']['style']['spacing']['blockGap'])) {
			$block['attrs']['style']['spacing']['blockGap'] = [
				'left' => '0px',
			];
		}

		if (isset($block['attrs']['style']['spacing']['blockGap'])) {
			foreach ($block['attrs']['style']['spacing']['blockGap'] as $key => $value) {
				$styles = [];
				if (strpos($value, 'var:preset|spacing|') !== false) {
					$value = str_replace('var:preset|spacing|', 'var(--wp--preset--spacing--', $value) . ')';
				}

				switch ($key) {
					case 'left':
						$styles[] = 'column-gap:' . $value;
						break;
					case 'top':
						$styles[] = 'row-gap:' . $value;
						break;
				}
			}
			$style_string = implode(';', array_filter($styles)) . ';';


			if (preg_match('/^\s*<div([^>]*?)style="([^"]*)"/', $block_content, $matches)) {
			    $existing_styles = $matches[2];
			    $block_content = preg_replace(
			        '/^\s*<div([^>]*?)style="([^"]*)"/',
			        '<div$1 style="' . $style_string . $existing_styles . '"',
			        $block_content,
			        1
			    );
			} else {
			    $block_content = preg_replace(
			        '/^\s*<div/',
			        '<div style="' . $style_string . '"',
			        $block_content,
			        1
			    );
			}
		}
	}

	return $block_content;
}

add_filter('render_block', 'lvl_render_block_filter__columns', 99, 2);