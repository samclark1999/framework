<?php

add_action('init', function () {
	if (class_exists('Level\PostTypes')) {
		(new Level\PostTypes)->registerCPT(
				'testimonial',
				'Testimonial',
				'Testimonials',
				[],
				'private',
				[
						'capability_type' => 'post',
						'rewrite'         => array('slug' => 'testimonial', 'with_front' => false),
						'menu_icon'       => 'dashicons-testimonial',
						'supports'        => array('title', 'thumbnail', 'custom-fields'),
				]
		);
	}
});