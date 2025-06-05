<?php
/**
 * lvl_child_register_all_post_types function.
 * Registers all custom post types for the theme. Hooks into init.
 * @access public
 * @return void
 */

add_action( 'init', 'lvl_child_register_all_post_types' );
function lvl_child_register_all_post_types(): void {

	// Landing Page
	( new Level\PostTypes )->registerCPT(
		'landing-page',
		'Landing Page',
		'Landing Pages',
		[],
		'private',
		[
			'capability_type' => 'post',
			'rewrite'         => array( 'slug' => 'lp', 'with_front' => false ),
			'menu_icon'       => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/> <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/> </svg>'),
			'supports'        => array( 'editor', 'title', 'custom-fields', 'page-attributes' ),
		]
	);

	// Resource
	( new Level\PostTypes )->registerCPT(
		'resource',
		'Resource',
		'Resources',
		[],
		'public',
		[
			'capability_type' => 'post',
			'hierarchical'   => false,
			'rewrite'         => array( 'slug' => 'resources', 'with_front' => false ),
			'menu_icon'       => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-collection-fill" viewBox="0 0 16 16"> <path d="M0 13a1.5 1.5 0 0 0 1.5 1.5h13A1.5 1.5 0 0 0 16 13V6a1.5 1.5 0 0 0-1.5-1.5h-13A1.5 1.5 0 0 0 0 6zM2 3a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 0-1h-11A.5.5 0 0 0 2 3m2-2a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 0-1h-7A.5.5 0 0 0 4 1"/> </svg>'),
			'supports'        => array( 'editor', 'title', 'custom-fields', 'page-attributes', 'thumbnail' ),
		]
	);


	// Video
//	( new Level\PostTypes )->registerCPT(
//			'video',
//			'Video',
//			'Videos',
//			[],
//			'public',
//			[
//					'capability_type' => 'post',
//					'rewrite'         => array( 'slug' => 'videos', 'with_front' => false ),
//					'menu_icon'       => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-collection-play-fill" viewBox="0 0 16 16"> <path d="M2.5 3.5a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1zm2-2a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1zM0 13a1.5 1.5 0 0 0 1.5 1.5h13A1.5 1.5 0 0 0 16 13V6a1.5 1.5 0 0 0-1.5-1.5h-13A1.5 1.5 0 0 0 0 6zm6.258-6.437a.5.5 0 0 1 .507.013l4 2.5a.5.5 0 0 1 0 .848l-4 2.5A.5.5 0 0 1 6 12V7a.5.5 0 0 1 .258-.437"/> </svg>'),
//					'supports'        => array( 'editor', 'title', 'custom-fields', 'page-attributes' ),
//			]
//	);

}


/**
 * lvl_child_default_post_content function.
 * Sets the default content for new posts to a specific template.
 * @access public
 * @param mixed $content
 * @param mixed $post
 * @return void
 */

//add_filter('default_content', 'lvl_child_default_post_content', 10, 2);
function lvl_child_default_post_content($content, $post)
{
	if ($post->post_type !== 'post') {
		return $content;
	}

	// Template - Post
	$patterns = get_posts(array(
			'post_type'      => 'wp_block',
			'title'          => 'Template - Post',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
	));

	if (!empty($patterns)) {
		$post = get_post( $patterns[0]->ID);
		return $post->post_content;
	}

	return $content;
}











/**
 * Handle conflict between resources CPT and pages with resources slug
 */
add_action('init', 'resources_custom_rewrite_rules', 20);
function resources_custom_rewrite_rules() {
    // For pages with resources as parent slug (like /resources/learning-hub/)
    add_rewrite_rule(
        '^resources/([^/]+)/?$',
        'index.php?pagename=resources/$matches[1]',
        'top'
    );

    // For deeper nested pages (like /resources/learning-hub/info-kit/)
    add_rewrite_rule(
        '^resources/([^/]+)/([^/]+)/?$',
        'index.php?pagename=resources/$matches[1]/$matches[2]',
        'top'
    );

    // Make sure CPT single views still work
    // This will check if the URL matches a resource post first
    global $wp_rewrite;
    add_rewrite_tag('%resource%', '([^/]+)', 'resource=');
    add_permastruct('resource', 'resources/%resource%', array(
        'with_front' => false,
        'paged' => false
    ));

    // Uncomment temporarily to flush rewrite rules
//     flush_rewrite_rules();
}

/**
 * Priority check to determine if URL should be a page or resource post
 */
add_filter('request', 'resources_url_priority_filter');
function resources_url_priority_filter($query_vars) {
    // Only run on front-end
    if (is_admin()) {
        return $query_vars;
    }

    // Check if we're potentially looking at a resources URL
    if (isset($query_vars['pagename']) && strpos($query_vars['pagename'], 'resources/') === 0) {
        // Extract the slug after resources/
        $slug_parts = explode('/', $query_vars['pagename']);
        array_shift($slug_parts); // Remove 'resources'

        if (empty($slug_parts)) {
            return $query_vars; // This is just /resources/ archive
        }

        $post_slug = $slug_parts[0];

        // Check if this matches a resource post
        $resource_post = get_posts([
            'name' => $post_slug,
            'post_type' => 'resource',
            'post_status' => 'publish',
            'posts_per_page' => 1
        ]);

        if (!empty($resource_post)) {
            // This is a resource post, modify query vars
            unset($query_vars['pagename']);
            $query_vars['resource'] = $post_slug;
            $query_vars['post_type'] = 'resource';
            $query_vars['name'] = $post_slug;
        }
    }

    return $query_vars;
}

/**
 * Fix permalinks for resources
 */
add_filter('post_type_link', 'resource_permalink_filter', 10, 2);
function resource_permalink_filter($permalink, $post) {
    if ($post->post_type !== 'resource') {
        return $permalink;
    }

    // Make sure the permalink structure is preserved
    $permalink = home_url('/resources/' . $post->post_name . '/');

    return $permalink;
}

/**
 * Ensure that the archive URL /resources/ shows the CPT archive
 */
add_filter('pre_get_posts', 'resources_archive_filter');
function resources_archive_filter($query) {
    // Only modify main queries on the front-end
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    // Check if we're on the resources archive
    if ($query->is_post_type_archive('resource') ||
        (isset($query->query['pagename']) && $query->query['pagename'] === 'resources')) {
        $query->set('post_type', 'resource');
        $query->is_archive = true;
        $query->is_post_type_archive = true;
        $query->is_page = false;
        $query->is_singular = false;
    }
}