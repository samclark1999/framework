<?php
function lvl_banner_enqueue_block_styles() {
	wp_enqueue_style( 'lvl-block-css-banner-init' );
}

add_action( 'wp_enqueue_scripts', 'lvl_banner_enqueue_block_styles' );