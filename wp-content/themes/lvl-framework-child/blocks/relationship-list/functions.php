<?php
// add possible taxonomies to acf field select
add_filter('acf/load_field/name=taxonomy', 'lvl_acf_taxonomy_field_choices');
function lvl_acf_taxonomy_field_choices($field)
{
	$field['choices'] = ['none' => 'Select a taxonomy'];
	$taxonomies = get_taxonomies(['public' => true], 'objects');
	foreach ($taxonomies as $taxonomy) {
		$field['choices'][$taxonomy->name] = $taxonomy->label;
	}
	return $field;
}