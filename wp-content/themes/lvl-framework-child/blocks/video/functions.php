<?php

add_action('init', 'lvl_register_video_block_styles');

function lvl_register_video_block_styles()
{

	register_block_style('lvl/video', array(
			'name'  => 'drop-shadow-dots-green',
			'label' => __('Drop Shadow Green', 'theme'),
	));
	register_block_style('lvl/video', array(
			'name'  => 'drop-shadow-dots-purple',
			'label' => __('Drop Shadow Purple', 'theme'),
	));

}