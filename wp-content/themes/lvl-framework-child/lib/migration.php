<?php

// scrub content of elementor shortcodes e.g.[elementor-template id=”383″]
function scrub_elementor_shortcode($content)
{
	$pattern = '/\[elementor-template.*?\]/';
	return preg_replace($pattern, '', $content);
}

add_filter('the_content', 'scrub_elementor_shortcode');