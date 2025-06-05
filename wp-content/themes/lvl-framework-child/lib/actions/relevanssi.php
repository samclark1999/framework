<?php
add_filter( 'relevanssi_render_blocks', function( $render, $_post ) {
	return false;
}, 10, 2 );


//add_filter( 'relevanssi_block_to_render', 'remove_blocks_from_search', 10, 2 );
function remove_blocks_from_search($pre_render, $parsed_block)
{
	$remove_blocks = [
			'core/spacer',
			'core/image',
			'core/post-title',
			'core/featured-image',
			'core/post-featured-image',
			'lvl/image-credit',
			'core/gallery',
			'core/audio',
			'core/video',
			'core/cover',
			'core/file',
			'core/html',
			'core/shortcode',
			'core/latest-comments',
			'core/latest-posts',
			'core/calendar',
			'core/rss',
			'core/separator',
			'core/search',
			'core/comments',
			'core/block', // global pattern

			'lvl/banner-showcase',
			'lvl/comments',
			'lvl/modal',
			'lvl/more-top-stories',
			'lvl/post-listing-top-articles',
			'lvl/post-tags',
			'lvl/recent-headlines',
			'lvl/related-stories',
			'lvl/remote-form',
			'lvl/remote-form-pattern-display',
			'lvl/target-topic-pattern-display',

			'gravityforms/form',
	];

//	var_dumped($parsed_block['blockName']);

	if (in_array($parsed_block['blockName'], $remove_blocks)) {
//		var_dumped('removing block');
		return '';
	}

	// if "metadata":{"name":"Bullet List"} is present, remove the block with the metadata
//	if (isset($parsed_block['attrs']['metadata']['name']) && $parsed_block['attrs']['metadata']['name'] === 'Bullet List') {
//		return '';
//	}

	return $pre_render;
}