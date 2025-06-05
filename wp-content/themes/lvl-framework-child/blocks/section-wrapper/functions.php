<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'lvl_register_section_wrapper_block_styles' );

function lvl_register_section_wrapper_block_styles() {
	register_block_style( 'lvl/section-wrapper', array(
		'name'  => 'overlay-50-dark',
		'label' => __( 'Left Overlay 50% Dark', 'theme' ),
	) );
	register_block_style( 'lvl/section-wrapper', array(
		'name'  => 'overlay-50-light',
		'label' => __( 'Left Overlay 50% Light', 'theme' ),
	) );
	register_block_style( 'lvl/section-wrapper', array(
		'name'  => 'overlay-50-dark--right',
		'label' => __( 'Right Overlay 50% Dark', 'theme' ),
	) );
	register_block_style( 'lvl/section-wrapper', array(
		'name'  => 'overlay-50-light--right',
		'label' => __( 'Right Overlay 50% Light', 'theme' ),
	) );
}