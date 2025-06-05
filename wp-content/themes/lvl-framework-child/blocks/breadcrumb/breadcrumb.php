<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$render = function ( $block, $is_preview, $content ) {

	$the_block = new Level\Block( $block );

	ob_start(); ?>
	<?php
	$the_block->addClass( 'my-3' );
	$the_block->addAttribute( [
		'aria-label' => __( 'Breadcrumb', 'theme' ),
	] );

	if ( $is_preview ) {
//		echo $the_block->previewNotice( 'info', __( 'Breadcrumb path of this page.', 'theme' ) );
	}

//	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
//		rank_math_the_breadcrumbs();
//	} elseif ( function_exists( 'yoast_breadcrumb' ) ) {
//        yoast_breadcrumb( '<div id="breadcrumbs">', '</div>' );
//	}
//    else {

		?>
        <nav <?php echo $the_block->getAttribute(); ?>>
            <ul class="breadcrumb text-body">
				<?php
				$home     = [
					'title' => __( 'Home', 'theme' ),
					'url'   => home_url(),
//					'icon'  => 'home',
				];
				$crumbs   = [];
				$crumbs[] = $home;

				if ( get_post_type( get_the_ID() ) === 'page' ) {
					$ancestors = get_post_ancestors( get_the_ID() );
					foreach ( array_reverse( $ancestors ) as $ancestor ) {
						$crumbs[] = [
							'title' => get_the_title( $ancestor ),
							'url'   => get_permalink( $ancestor ),
						];
					}
					$crumbs[] = [
						'title' => get_the_title(),
						'url'   => get_permalink(),
					];
				} elseif ( is_category() ) {
					$categories = get_the_category();
					if ( $categories ) {
						$category = $categories[0];
						$crumbs[] = [
							'title' => $category->name,
							'url'   => get_category_link( $category->term_id ),
						];
					}
				} elseif ( is_tax() ) {
//                var_dumped('tax');

					// get taxonomy
					$tax      = get_taxonomy( get_queried_object()->taxonomy );
					$crumbs[] = [
						'title' => $tax->label,
						//                    'url'   => get_term_link($tax),
					];

					$term     = get_queried_object();
					$crumbs[] = [
						'title' => $term->name,
						'url'   => get_term_link( $term ),
					];
				} elseif ( is_single() ) {
//                var_dumped('single');
					// add post type archive
					$post_type    = get_post_type();
					$archive_page = get_option( $post_type . '_archive_page' );

					if ( $post_type === 'post' /*|| $archive_page === false*/ ) {
						$archive_page = get_option( 'page_for_posts' );
					}

					if ( $archive_page ) {
						$page     = get_post( $archive_page );
						$crumbs[] = [
							'title' => $page->post_title,
							'url'   => get_permalink( $archive_page ),
						];
					}

					$crumbs[] = [
						'title' => get_the_title(),
						'url'   => get_permalink(),
					];
				} elseif ( is_archive() || is_home() ) {
//                var_dumped('archive');
					$post_type    = get_post_type();
					$archive_page = get_option( $post_type . '_archive_page' );

					if ( $post_type === 'post' || $archive_page === false ) {
						$archive_page = get_option( 'page_for_posts' );
					}

					if ( ! $archive_page ) {
//                    $crumbs[] = [
//                        'title' => get_the_title(),
//                        'url'   => get_permalink(),
//                    ];
					} else {
						$page     = get_post( $archive_page );
						$crumbs[] = [
							'title' => $page->post_title,
							'url'   => get_permalink( $archive_page ),
						];
					}
				} elseif ( is_search() ) {
					$crumbs[] = [
						'title' => __( 'Search results', 'theme' ),
						'url'   => get_search_link(),
					];
				} elseif ( is_404() ) {
					$crumbs[] = [
						'title' => __( 'Page not found', 'theme' ),
						'url'   => '',
					];
				}
				$crumbs = apply_filters( 'lvl/breadcrumb/crumbs', $crumbs );
				//            var_dumped($crumbs);
				foreach ( $crumbs as $index => $crumb ) {
					$classes = [];
					if ( $index === 0 ) {
						$classes[] = 'breadcrumb-item';
						$classes[] = 'breadcrumb-item-home';
					} elseif ( $index === count( $crumbs ) - 1 ) {
						$classes[] = 'breadcrumb-item-active';
					} else {
						$classes[] = 'breadcrumb-item';
					}
					?>
                <li class="<?php echo implode( ' ', $classes ); ?>">
					<?php if ( $crumb['url'] ) { ?>
						<?php
						$label = $crumb['title'];
						if ( $crumb['icon'] ?? false ) {
							$icon  = $crumb['icon'];
							$label = do_shortcode( '[icon icon="' . $icon . '" url="" size="sm" xclass=""]' );
						}

						// if is last index, don't make it a link
						if ( $index === count( $crumbs ) - 1 ) {
							$element = ( get_field( 'header_level' ) ?: 'h1' );
							?>
                            <<?php echo $element; ?> class="text-inherit"><?php echo $label; ?></<?php echo $element; ?>>

						<?php } else {
							?>
                            <a class="" href="<?php echo esc_url( $crumb['url'] ); ?>"><?php echo $label; ?></a>
							<?php
						}
					} else { ?>
						<?php echo $crumb['title']; ?>
					<?php } ?>
                    </li>
				<?php } ?>
            </ul>
        </nav>
		<?php
//	}

	$output = ob_get_clean();

	echo $the_block->renderSection( $output, 'basic' );

};

$render( $block, $is_preview, $content );