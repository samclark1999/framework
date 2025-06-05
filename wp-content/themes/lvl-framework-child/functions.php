<?php if (!defined('ABSPATH')) {
	exit;
}
/**
 * Main functions file.
 * This file contains all functions to add theme support and remove default WordPress functionality.
 */

if (!defined('LVL_THEME_URI_CHILD'))
	define("LVL_THEME_URI_CHILD", get_stylesheet_directory_uri());


add_action('after_setup_theme', function () {
	//
});


add_action('wp_enqueue_scripts', 'lvl_child_load_scripts', 10);
function lvl_child_load_scripts(): void
{
	wp_register_script('lvl-bootstrap', LVL_THEME_URI_CHILD . lvl_cache_bust('/dist/js/bootstrap.min.js'), [], null, true);
	wp_enqueue_script('lvl-child-scripts', LVL_THEME_URI_CHILD . lvl_cache_bust('/dist/js/app.min.js'), ['lvl-main-scripts'], null, true);
	// BLOCKS
	wp_enqueue_script('lvl-blocks-scripts');
}

add_action('wp_enqueue_scripts', 'lvl_child_load_styles', 99);
function lvl_child_load_styles(): void
{
	wp_register_style('lvl-bootstrap', LVL_THEME_URI_CHILD . lvl_cache_bust('/dist/css/bootstrap.min.css'), [], null, 'all');
	wp_enqueue_style('lvl-child-styles', LVL_THEME_URI_CHILD . lvl_cache_bust('/dist/css/' . 'app.min.css'), ['lvl-base-styles', 'lvl-bootstrap'], null, 'all');
	// BLOCKS
	wp_enqueue_style('lvl-blocks-styles', LVL_THEME_URI_CHILD . lvl_cache_bust('/dist/css/blocks.min.css'), [], null, 'all');
}

// Enqueue fonts to front-end and editor
add_action('wp_enqueue_scripts', 'lvl_child_load_fonts', 99);
add_action('enqueue_block_editor_assets', 'lvl_child_load_fonts');
function lvl_child_load_fonts(): void
{
//	wp_enqueue_style('lvl-fonts', LVL_THEME_URI_CHILD . lvl_cache_bust('/dist/css/fonts.min.css'), false);
	wp_enqueue_style('lvl-typekit-fonts', 'https://use.typekit.net/jtc0brf.css', [], null, 'all');
	// wp_enqueue_style('lvl-google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap', [], null, 'all');
}

/**
 * Adds custom login styles to the default WordPress login screen.
 * @return void
 */
add_action('login_enqueue_scripts', 'lvl_child_login_styles', 10);
function lvl_child_login_styles()
{
	wp_enqueue_global_styles();
	wp_enqueue_style('lvl-login-stylesheet', LVL_THEME_URI_CHILD . lvl_cache_bust('/dist/admin/css/login.min.css'), false);
	wp_enqueue_style('lvl-typekit-fonts', 'https://use.typekit.net/jtc0brf.css', [], null, 'all');

}


// Enqueue styles for the block editor
add_action('enqueue_block_editor_assets', 'lvl_child_block_editor_assets');
function lvl_child_block_editor_assets(): void
{
	wp_enqueue_style('lvl-child-block-editor-bs-styles', LVL_THEME_URI_CHILD . '/dist/admin/css/editor-bs.min.css?v=' . time(), [], null, 'all');
	wp_enqueue_style('lvl-child-block-editor-theme-styles', LVL_THEME_URI_CHILD . '/dist/admin/css/editor-theme.min.css?v=' . time(), ['lvl-child-block-editor-bs-styles'], null, 'all');
//	wp_enqueue_style('lvl-child-block-editor-assets-styles', LVL_THEME_URI_CHILD . '/dist/admin/css/editor.min.css?v=' . time(), [], null, 'all');
	wp_enqueue_style('lvl-blocks-editor-styles', LVL_THEME_URI_CHILD . lvl_cache_bust('/dist/css/blocks.min.css'), [], null, 'all');

	// SCRIPTS
	wp_enqueue_script('lvl-child-block-editor-assets-js', LVL_THEME_URI_CHILD . lvl_cache_bust('/dist/js/editor.min.js'), ['lvl-block-editor-assets-js'], null, true);
	wp_enqueue_script('lvl-blocks-editor-scripts', LVL_THEME_URI_CHILD . lvl_cache_bust('/dist/js/blocks.min.js'), [], null, true);
}

// Enqueue styles for the Admin Settings page
add_action('admin_enqueue_scripts', 'lvl_child_admin_styles');
function lvl_child_admin_styles(): void
{
	wp_enqueue_style('lvl-child-admin-styles', LVL_THEME_URI_CHILD . lvl_cache_bust('/dist/admin/css/admin.min.css'), [], null, 'all');
}

// Remove button block supports
add_filter('block_type_metadata_settings', 'disable_button_block_supports', 10, 2);
function disable_button_block_supports($settings, $metadata)
{
	if ($metadata['name'] === 'core/button') {
		unset($settings['supports']['spacing']);
		unset($settings['supports']['typography']);
		unset($settings['supports']['color']);
		unset($settings['supports']['border']);
		unset($settings['supports']['__experimentalBorder']);
		unset($settings['supports']['shadow']);
	}

	return $settings;
}

register_sidebar([
		'name'          => __( 'Footer - LPs' ),
		'id'            => 'lvl-footer-lp',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'description'   => __( 'Use this to build the footer for Landing Pages' ),
		'before_title'  => '<h5>',
		'after_title'   => '</h5>'
]);

/**
 * Add CPTs and Taxonomies
 * To override the default post types and taxonomies, create a new file in the lib directory and include it here.
 * */
locate_template('lib/post-types-child.php', true, true);
locate_template('lib/taxonomies-child.php', true, true);

locate_template('lib/actions/gravityforms.php', true, true);
locate_template('lib/actions/shortcodes.php', true, true);