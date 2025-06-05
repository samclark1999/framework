<?php
/**
 * lvl_child_register_all_taxonomies function.
 *
 * Registers all custom taxomonies used by the theme.
 *
 * @access public
 * @return void
 */
add_action('init', 'lvl_child_register_all_taxonomies');
function lvl_child_register_all_taxonomies(): void
{

	(new Level\Taxonomies)->registerTax(
			'resource_type',
			['post', 'resource', 'video'],
			'Resources Type',
			'Resource Types'
	);

	//addTaxonomyToPostType
	(new Level\Taxonomies)->addTaxonomyToPostType('category', 'resource');
	(new Level\Taxonomies)->addTaxonomyToPostType('category', 'page');
}