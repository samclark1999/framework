<?php

add_action( 'widgets_init', function() {

	register_sidebar([
		'name'          => __( 'Footer' ),
		'id'            => 'lvl-footer',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'description'   => __( 'Use this to build the footer' ),
		'before_title'  => '<h5>',
		'after_title'   => '</h5>'
	]);

	register_sidebar([
		'name'          => __( '404 Content' ),
		'id'            => 'lvl-404',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'description'   => __( 'Use this to build the 404 page' ),
		'before_title'  => '<h5>',
		'after_title'   => '</h5>'
	]);
});