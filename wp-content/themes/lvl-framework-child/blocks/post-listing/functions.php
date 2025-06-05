<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'acf/load_field/key=field_6596f612762a3', 'acf_populate_post_types' );

function acf_populate_post_types( $field ) {
	$field['choices'] = array();

	$post_types = get_post_types( array(
		'public' => true,
	), 'objects' );

	foreach ( $post_types as $post_type ) {
		if ( in_array( $post_type->name, [
			'page',
			'attachment',
			'banner',
			'landing-page',
			'team-member',
			'location',
		] ) ) {
			continue;
		}
		$field['choices'][ $post_type->name ] = $post_type->labels->name;
	}

	return $field;
}


// load taxonomy terms in key field_6596f63a762a4
add_filter( 'acf/load_field/key=field_6596f63a762a4', 'acf_populate_taxonomies' );
add_filter( 'acf/load_field/key=field_taxonomies_pre_filter_key', 'acf_populate_taxonomies' );

function acf_populate_taxonomies( $field ) {
	$field['choices'] = array();


	$post_types = get_post_types( array(
//		'public' => true,
	), 'objects' );

	$values = []; // list of post types that have already been added to the choices

	foreach ( $post_types as $post_type ) {
		if ( in_array( $post_type->name, [
			'page',
			'attachment',
			'banner',
			'landing-page',
			'team-member',
			'location',
		] ) ) {
			continue;
		}


		$taxonomies = get_object_taxonomies( $post_type->name, 'objects' );
		foreach ( $taxonomies as $taxonomy ) {
			if ( in_array( $taxonomy->name, [
				// WP Post Types
				'post_format',
				'nav_menu',
				'link_category',
				'post_tag',
				'wp_pattern_category',
				'wp_theme',
				'wp_template_part_area',
				// theme CPTs
				'testimonial_cat',
			] ) ) {
				continue;
			}

			$values[ $taxonomy->name ][] = $post_type->labels->name;

//            if (key_exists($taxonomy->name, $field['choices'])) continue;

//            $field['choices'][$taxonomy->name] = '(' . $post_type->name . ') ' . $taxonomy->labels->name;
		}
	}

	foreach ( $values as $taxonomy => $post_types ) {
		$field['choices'][ $taxonomy ] = get_taxonomy( $taxonomy )->labels->name . '<em> (' . implode( '/', $post_types ) . ')</em>';
	}

	return $field;
}

add_filter( 'acf/load_field/key=field_terms_pre_filter_key', 'lvl_populate_pre_filter_terms' );
function lvl_populate_pre_filter_terms( $field ) {
	$field['choices'] = array();

	$taxonomies = get_field( 'taxonomies_pre_filter' );

	if ( empty( $taxonomies ) || ! is_array( $taxonomies ) ) {
		return $field;
	}

	$terms = get_terms( array(
		'taxonomy'   => $taxonomies,
		'hide_empty' => false,
	) );

	if ( is_wp_error( $terms ) ) {
		return $field;
	}

	foreach ( $terms as $term ) {
		$field['choices'][ $term->term_id ] = $term->name . ' (' . $term->taxonomy . ')';
	}

	return $field;
}



add_action( 'init', 'lvl_register_post_listing_block_styles' );

function lvl_register_post_listing_block_styles() {
	register_block_style( 'lvl/post-listing', array(
		'name'  => 'plain',
		'label' => __( 'Plain', 'theme' ),
	) );

	register_block_style( 'lvl/post-listing', array(
		'name'  => 'basic',
		'label' => __( 'Basic', 'theme' ),
	) );
}


add_action( 'wp_ajax_lvl_get_taxonomy', 'lvl_get_taxonomy' );
function lvl_get_taxonomy() {
	$post_types = get_post_types( array(
		'public' => true,
	), 'names' );

	$all_taxonomies = get_taxonomies( array(
//		'public' => true,
	), 'objects' );


	$taxonomies = array_filter( $all_taxonomies, function( $taxonomy ) use ( $post_types ) {
		$object_types = $taxonomy->object_type;
		foreach ( $object_types as $object_type ) {
			if ( in_array( $object_type, $post_types ) ) {
				return true;
			}
		}
		return false;
	} );

	// flatten to names
	$taxonomies = array_map( function( $taxonomy ) {
		return $taxonomy->object_type;
	}, $taxonomies );


	echo json_encode( ["taxonomies" => $taxonomies] );

	die();
}

//lvl_get_taxonomy_terms
add_action( 'wp_ajax_lvl_get_taxonomy_terms', 'lvl_get_taxonomy_terms' );
function lvl_get_taxonomy_terms() {
	$taxonomy = isset($_POST['taxonomy']) ? json_decode(stripslashes($_POST['taxonomy']), true) : null;

	// If taxonomy is null
	if (is_null($taxonomy)) {
		echo json_encode([]);
		die();
	}

	$terms = get_terms($taxonomy, array(
		'hide_empty' => false,
	));

	if (is_wp_error($terms)) {
		echo json_encode([]);
		die();
	}

	// Format the terms as an associative array
	$formatted_terms = [];
	foreach ($terms as $term) {
		$taxonomy_label = get_taxonomy($term->taxonomy)->labels->name;
		$formatted_terms[$term->term_id] = $term->name . ' [' . $taxonomy_label . ']';
	}

	asort($formatted_terms);


	echo json_encode($formatted_terms);
	die();
}