<?php namespace Level;

/**
 * CustomPostTypes class.
 * Registers all custom post types for the theme. Hooks into init.
 */
class PostTypes {
	private string $singular;

	public function __construct() {
		// Register custom post type framework
	}

	/**
	 * Registers a custom post type.
	 *
	 * @param string $postType
	 * @param string $singular
	 * @param string $plural
	 * @param array  $features
	 * @param string $type (public, private, hidden)
	 * @param array  $args
	 *
	 * @return void
	 */
	public function registerCPT( string $postType, string $singular, string $plural, array $features = [], string $type = 'public', array $args = [] ): void {
		// Set the singular and plural labels
		$this->singular = $singular;
		$labels         = $this->getCPTLabels( $singular, $plural );

		$features = $this->validateSupports($features);

		// Default options for custom post type
		$default_args = $this->getDefaults($type);
//		$default_args = array(
//			'labels'              => $labels,
//			'description'         => $singular . ' custom post type.',     // Optional

//			'public'              => true,   // generally this should always be set to true.
//			'show_ui'             => true,   // generally this should always be set to true.
//			'has_archive'         => true,  // generally this should always be set to true.
//			'show_in_rest'        => true,
//			'show_in_menu'        => true,   // generally this should always be set to true.
//			'show_in_nav_menus'   => true,  // set to true if you want to use singular items in nav menus.
//			'exclude_from_search' => false,   // set to false if you want to show this in search results.
//			'capability_type'     => 'page', // post or page
//			'map_meta_cap'        => true,   // generally should always be set to true.
//			'hierarchical'        => false,  // false for time-based (aka newest posts first.)
//			'rewrite'             => [ 'slug' => _wp_to_kebab_case( $singular ), 'with_front' => false ],
//			'query_var'           => true,
//			'menu_icon'           => 'dashicons-superhero', // https://developer.wordpress.org/resource/dashicons/
//			'supports'            => [ 'title', 'thumbnail' ],
//		);

		$args = wp_parse_args( $args, $default_args );

		$args['labels'] = $labels;
		$args['description'] = $singular . ' custom post type.';

		register_post_type( $postType, $args );

		foreach ( $features as $feat ) {
			$support = get_theme_support( $feat );
			if ( ! is_array( $support ) ) {
				$support = array( array() );
			} elseif ( ! isset( $support[0] ) || ! is_array( $support[0] ) ) {
				$support[0] = array();
			}
			$support[0][] = $postType;

			add_theme_support( $feat, $support[0] );
		}
	}

	/**
	 * Retrieves the labels for a custom post type.
	 *
	 * @param string $singular
	 * @param string $plural
	 *
	 * @return array
	 */
	private function getCPTLabels( string $singular, string $plural ): array {
		return array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'add_new'               => 'Add New',
			'add_new_item'          => 'Add New ' . $singular,
			'edit_item'             => 'Edit ' . $singular,
			'new_item'              => 'New ' . $singular,
			'view_item'             => 'View ' . $singular,
			'search_items'          => 'Search ' . $plural,
			'not_found'             => 'No ' . $plural . ' found',
			'not_found_in_trash'    => 'No ' . $plural . ' found in trash',
			'parent_item_colon'     => 'Parent ' . $singular . ':',
			'all_items'             => 'All ' . $plural,
			'archives'              => $plural . ' Archives',
			'insert_into_item'      => 'Insert into ' . $singular,
			'uploaded_to_this_item' => 'Uploaded to this ' . $singular,
			'featured_image'        => 'Featured Image',
			'set_featured_image'    => 'Set Featured Image',
			'remove_featured_image' => 'Remove Featured Image',
			'use_featured_image'    => 'Use as Featured Image',
			'menu_name'             => $plural,
			'name_admin_bar'        => $singular,
		);
	}

	private function getDefaults( $type = 'public' ): array {
		$defaults = array(
			'public'              => true,
			'show_ui'             => true,
			'has_archive'         => true,
			'show_in_rest'        => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'exclude_from_search' => false,
			'capability_type'     => 'page',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'rewrite'             => [ 'slug' => _wp_to_kebab_case( $this->singular ), 'with_front' => false ],
			'query_var'           => true,
			'menu_icon'           => 'dashicons-superhero',
			'supports'            => [ 'title', 'thumbnail' ],
		);

		if ( 'private' === $type ) {
			$defaults['public']              = true;
			$defaults['show_ui']             = true;
			$defaults['has_archive']         = false;
			$defaults['show_in_rest']        = true;
			$defaults['show_in_menu']        = true;
			$defaults['show_in_nav_menus']   = false;
			$defaults['exclude_from_search'] = true;
			$defaults['capability_type']     = 'page';
			$defaults['map_meta_cap']        = true;
			$defaults['hierarchical']        = false;
			$defaults['rewrite']             = [ 'slug' => _wp_to_kebab_case( $this->singular ), 'with_front' => false ];
			$defaults['query_var']           = true;
			$defaults['menu_icon']           = 'dashicons-superhero';
			$defaults['supports']            = [ 'title', 'thumbnail' ];
		} elseif ( 'hidden' === $type ) {
			$defaults['public']              = false;
			$defaults['show_ui']             = false;
			$defaults['has_archive']         = false;
			$defaults['show_in_rest']        = false;
			$defaults['show_in_menu']        = false;
			$defaults['show_in_nav_menus']   = false;
			$defaults['exclude_from_search'] = true;
			$defaults['capability_type']     = 'page';
			$defaults['map_meta_cap']        = true;
			$defaults['hierarchical']        = false;
			$defaults['rewrite']             = [ 'slug' => _wp_to_kebab_case( $this->singular ), 'with_front' => false ];
			$defaults['query_var']           = true;
			$defaults['menu_icon']           = 'dashicons-superhero';
			$defaults['supports']            = [ 'title', 'thumbnail' ];
		}

		return $defaults;

	}

	private function validateSupports($supports): array
	{
		$valid_supports = [
				'admin-bar',
				'align-wide',
				'appearance-tools',
				'automatic-feed-links',
				'block-templates',
				'block-template-parts',
				'border',
				'core-block-patterns',
				'custom-background',
				'custom-header',
				'custom-line-height',
				'custom-logo',
				'customize-selective-refresh-widgets',
				'custom-spacing',
				'custom-units',
				'dark-editor-style',
				'disable-custom-colors',
				'disable-custom-font-sizes',
				'disable-custom-gradients',
				'disable-layout-styles',
				'editor-color-palette',
				'editor-gradient-presets',
				'editor-font-sizes',
				'editor-spacing-sizes',
				'editor-styles',
				'featured-content',
				'html5',
				'link-color',
				'menus',
				'post-formats',
				'post-thumbnails',
				'responsive-embeds',
				'starter-content',
				'title-tag',
				'widgets',
				'widgets-block-editor',
				'wp-block-styles',
		];

		$valid = [];
		foreach ($supports as $support) {
			if (in_array($support, $valid_supports)) {
				$valid[] = $support;
			}
		}

		return $valid;
	}
}