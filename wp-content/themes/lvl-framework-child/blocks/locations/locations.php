<?php
/**
 * Block Name: Map
 *
 * This is the template that displays the map block.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$render = function ( $block, $is_preview, $content ) {

	$the_block = new Level\Block( $block );

	$cards_per_row = $the_block->getField( 'cards_per_row' ) ?: 3;
	$locations     = $the_block->getField( 'locations' ) ?: [];

	if ( ! $locations ) {
		$locations = get_posts( [
			'post_type'      => 'location',
			'posts_per_page' => - 1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'fields'         => 'ids',
		] );
	}

	$the_block->addStyle( '--card-count:' . $cards_per_row );

	ob_start();
	?>
    <div class="cards justify-content-center">
		<?php
		foreach ( $locations as $location ) {
			?>
            <div class="card-inner">
                <div class="card-body bg-white shadow-sm p-3" data-lvl-stretch-link="true">
					<?php
					if ( get_field( 'linkedin', $location ) ) {
						?>
                        <a href="<?php echo get_field( 'linkedin', $location ); ?>" class="card-linkedin" rel="noopener noreferrer">
                            <?php echo do_shortcode( '[icon icon="linkedin-in" url="" size="sm" xclass=""]' ); ?>
                        </a>
						<?php
//						$linkedin = '<!-- wp:social-links {"size":"has-small-icon-size"} --><ul class="wp-block-social-links has-small-icon-size float-end"><!-- wp:social-link {"url":"' . ( get_field( 'linkedin', $location ) ?: '' ) . '","service":"linkedin"} /--></ul><!-- /wp:social-links -->';
//						$linkedin = parse_blocks( $linkedin );
//						foreach ( $linkedin as $item ) {
//							echo apply_filters( 'the_content', render_block( $item ?? '' ) );
//						}
						?>
						<?php
					}
					?>
                    <h3 class="card-title"><?php echo( get_field( 'display_title', $location ) ?: get_the_title( $location ) ); ?></h3>
                    <address class="card-address mb-0"><?php echo get_field( 'display_address', $location ); ?></address>
					<?php
					$phone = get_field( 'phone', $location );
					if ( $phone ) {
						?>
                        <div class="card-phone"><a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a></div>
						<?php
					}
					?>
                    <a href="<?php echo get_permalink( $location ); ?>" class="btn btn-arrow">View Location
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="18" viewBox="0 0 18 9" fill="none">
                            <path d="M16 4.26099L16.7071 3.55388L17.4142 4.26099L16.7071 4.96809L16 4.26099ZM1 5.26099H0V3.26099H1V5.26099ZM13.2071 0.0538796L16.7071 3.55388L15.2929 4.96809L11.7929 1.46809L13.2071 0.0538796ZM16.7071 4.96809L13.2071 8.46809L11.7929 7.05388L15.2929 3.55388L16.7071 4.96809ZM16 5.26099H1V3.26099H16V5.26099Z" fill="currentColor"/>
                        </svg>
                    </a>
                </div>
            </div>
			<?php
		}
		?>
    </div>
	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection( $output );

};
$render( $block, $is_preview, $content );