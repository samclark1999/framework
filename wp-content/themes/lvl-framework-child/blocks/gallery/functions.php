<?php if (!defined('ABSPATH')) {
	exit;
}

add_action('init', 'lvl_register_gallery_block_styles');

function lvl_register_gallery_block_styles()
{
	register_block_style('lvl/gallery', array(
			'name'  => 'white-filter',
			'label' => __('White Filter', 'theme'),
	));

	register_block_style('lvl/gallery', array(
			'name'  => 'grayscale',
			'label' => __('Grayscale', 'theme'),
	));
}