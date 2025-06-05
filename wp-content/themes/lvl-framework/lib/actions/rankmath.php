<?php if (!defined('ABSPATH')) {
	exit;
}

/**
 * Filter to set noindex to specific post types
 */
add_filter( 'rank_math/frontend/robots', function ( $robots ) {

	$noindex_post_types = [
			'landing-page',
	];

	// Check if the current post type is in the noindex list
	if ( is_singular( $noindex_post_types ) ) {
		unset( $robots['index'] );
		$robots['noindex'] = 'noindex';
	}

	return $robots;
} );