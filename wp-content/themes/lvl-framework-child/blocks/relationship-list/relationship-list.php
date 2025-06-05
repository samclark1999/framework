<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$render = function ( $block, $is_preview, $content ) {

	$the_block = new Level\Block( $block );

	ob_start(); ?>
	<?php

	if ( $is_preview ) {
		$the_block->addClass( '--preview' );
	}

	$is_linked = get_field( 'is_linked' );
	$posts      = get_field( 'posts' ) ?: [];
	$taxonomies = get_field( 'taxonomy' ) ?: [];

	if ( ! is_array( $posts ) ) {
		$posts = [ $posts ];
	}
	if ( ! is_array( $taxonomies ) ) {
		$taxonomies = [ $taxonomies ];
	}

	?>
    <ul class="list-style-icon-brand">
		<?php
		foreach ( $posts as $post ) {
			if ( $is_linked ) {
				?>
                <li>
                    <a href="<?php echo get_permalink( $post ); ?>">
						<?php echo get_the_title( $post ); ?>
                    </a>
                </li>
				<?php
			} else {
				?>
                <li>
					<?php echo get_the_title( $post ); ?>
                </li>
				<?php
			}

		}

		foreach ( $taxonomies as $taxonomy ) {
			if ( $taxonomy === 'none' ) {
				continue;
			}
			$terms = wp_get_post_terms( get_the_ID(), $taxonomy );
			foreach ( $terms as $term ) {
				?>
                <li>
					<?php
					if ( $is_linked ) {
						?>
                        <a href="<?php echo get_term_link( $term->term_id, $taxonomy ); ?>">
							<?php echo $term->name; ?>
                        </a>
						<?php
					} else {
						echo $term->name;
					}
					?>
                </li>
				<?php
			}
		}
		?>
    </ul>
	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection( $output, 'basic' );

};

$render( $block, $is_preview, $content );