<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// include_once get_template_directory() . '/lib/blocks/styles.php'; // if you want to include the parent theme's block styles

add_action('init', 'lvl_register_image_block_styles');

function lvl_register_image_block_styles()
{
	/**
	 * Group
	 */
	register_block_style('core/group', array(
		'name'  => 'aspect-1-1',
		'label' => __('Aspect 1:1', 'theme'),
	));

	/**
	 * Image
	 */
	register_block_style('core/image', array(
		'name'  => 'circle',
		'label' => __('Circle', 'theme'),
	));
	register_block_style('core/image', array(
		'name'  => 'hover-effect',
		'label' => __('Hover Effect', 'theme'),
	));
	register_block_style('core/image', array(
		'name'  => 'drop-shadow-dots-green',
		'label' => __('Drop Shadow Green', 'theme'),
	));
	register_block_style('core/image', array(
		'name'  => 'drop-shadow-dots-purple',
		'label' => __('Drop Shadow Purple', 'theme'),
	));
	register_block_style('core/image', array(
		'name'  => 'top-fade-dark',
		'label' => __('Top Fade Dark', 'theme'),
	));
	register_block_style('core/image', array(
		'name'  => 'top-fade-light',
		'label' => __('Top Fade Light', 'theme'),
	));
	register_block_style('core/image', array(
		'name'  => 'bottom-fade-dark',
		'label' => __('Bottom Fade Dark', 'theme'),
	));
	register_block_style('core/image', array(
		'name'  => 'bottom-fade-light',
		'label' => __('Bottom Fade Light', 'theme'),
	));
}

add_action('init', 'lvl_register_column_block_styles');
function lvl_register_column_block_styles()
{
	register_block_style('core/column', array(
		'name'  => 'fit-content',
		'label' => __('Fit Content', 'theme'),
	));

	register_block_style('core/column', array(
		'name'  => 'flex-auto',
		'label' => __('Auto Width', 'theme'),
	));
}


// Button Styles
add_action( 'init', 'lvl_register_button_block_styles' );
function lvl_register_button_block_styles() {

	register_block_style( 'core/button', array(
		'name'         => 'btn-primary',
		'label'        => __( 'Primary', 'theme' ),
		'default'      => true
	));

	register_block_style( 'core/button', array(
		'name'         => 'btn-secondary',
		'label'        => __( 'Secondary', 'theme' )
	));

	register_block_style( 'core/button', array(
		'name'         => 'btn-primary-outline',
		'label'        => __( 'Primary Outline', 'theme' )
	));

	register_block_style( 'core/button', array(
		'name'         => 'btn-secondary-outline',
		'label'        => __( 'Secondary Outline', 'theme' )
	));

	register_block_style( 'core/button', array(
		'name'         => 'btn-white',
		'label'        => __( 'White', 'theme' )
	));

	register_block_style( 'core/button', array(
		'name'         => 'btn-white-outline',
		'label'        => __( 'White Outline', 'theme' )
	));

	register_block_style( 'core/button', array(
		'name'         => 'btn-link',
		'label'        => __( 'Link', 'theme' )
	));
}

// List Styles
add_action( 'init', 'lvl_register_list_block_styles' );
function lvl_register_list_block_styles() {

	register_block_style( 'core/list', array(
		'name'         => 'checkmark',
		'label'        => __( 'Checkmark', 'theme' ),
		'default'      => true
	));

	register_block_style( 'core/list', array(
		'name'         => 'dot',
		'label'        => __( 'Dot', 'theme' ),
		'default'      => true
	));

	register_block_style( 'core/list', array(
		'name'         => 'dash',
		'label'        => __( 'Dash', 'theme' ),
	));

	register_block_style( 'core/list', array(
		'name'         => 'circle',
		'label'        => __( 'Circle', 'theme' ),
	));

	register_block_style( 'core/list', array(
		'name'         => 'square',
		'label'        => __( 'Square', 'theme' ),
	));

	register_block_style( 'core/list', array(
		'name'         => 'none',
		'label'        => __( 'None', 'theme' ),
	));
}