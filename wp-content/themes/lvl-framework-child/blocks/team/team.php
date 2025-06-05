<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$render = function ( $block, $is_preview, $content ) {

	$the_block = new Level\Block( $block );

	$cards_per_row = ( $the_block->getField( 'cards_per_row' ) ?: 4 );
	$the_block->addStyle( '--card-count:' . $cards_per_row );

	$layout = $the_block->getField( 'layout' ) ?: 'grid';
	$the_block->addAttribute( [ 'data-layout' => $layout ] );

	$is_randomized = $the_block->getField( 'randomize_order' );
	$is_overflow   = $the_block->getField( 'is_overflow' );

	$autoplay   = $the_block->getField( 'slider_options:autoplay' ) ? 'true' : 'false';
	$delay      = $the_block->getField( 'slider_options:delay' );
	$speed      = $the_block->getField( 'slider_options:speed' );
	$pagination = $the_block->getField( 'slider_options:pagination' ) ? 'true' : 'false';
	$navigation = 'true';// $the_block->getField( 'slider_options:navigation' ) ? 'true' : 'false';
	$loop       = 'false';//$the_block->getField( 'slider_options:loop' ) ? 'true' : 'false';


	$types        = $the_block->getField( 'team_types' ) ?: get_terms( [
		'taxonomy'   => 'team-type',
		'hide_empty' => true,
		'exclude'    => 1,
		'fields'     => 'term_id',
	] );
	$team_members = $the_block->getField( 'team_members' ) ?: [];

	$args = [
		'post_type'      => 'team-member',
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
		'orderby'        => 'date',
		'order'          => 'ASC',
	];

	if ( $team_members ) {
		$args['post__in'] = $team_members;
		$args['orderby']  = 'post__in';
	}

	if ( $types ) {
		$args['tax_query'] = [
			[
				'taxonomy' => 'team-type',
				'field'    => 'term_id',
				'terms'    => $types,
			],
		];
	}

	$team = new WP_Query( $args );

	if ( $is_randomized ) {
		shuffle( $team->posts );
	}

	if ( $is_overflow ) {
		$the_block->addClass( 'overflow-to-edge' );
	}

    $is_public = get_post_type_object('team-member')->public;
    $is_modal = !$is_public;

    if ($is_modal) {
        $the_block->addAttribute(['data-display' => 'modal']);
    }

	ob_start();

	if ( $is_preview && $layout === 'slider' ) {
		echo $the_block->previewNotice( 'info', 'Displays as a slider on the front end.' );
	}

	if ( $is_preview ) {
		$layout = 'grid';
	}
	?>
    <div class="team" data-bs-theme="light">

        <div class="members">
            <div class="team-target">
                <div class="team-members <?php echo 'layout-' . $layout; ?><?php echo( $layout === 'grid' ? ' row' : '' ) ?>">
					<?php
					ob_start();
					if ( $team->have_posts() ) {
						while ( $team->have_posts() ) {
							$team->the_post();
							$team_post = $team->post;
							?>

                            <div class="member<?php echo( $layout === 'slider' ? ' swiper-slide' : '' ) ?> py-3">
                                <div class="member-inner" data-lvl-stretch-link="true">
                                    <div class="img">
										<?php
										echo wp_get_attachment_image( get_post_thumbnail_id( $team_post->ID ), 'medium_large', false, [ 'class' => 'img-fluid' ] );
										//echo get_the_post_thumbnail($team_post->ID, 'medium_large', ['class' => 'img-fluid']);
										?>
                                    </div>
                                    <div class="info justify-content-between">
                                        <div>
                                            <h4 class="h5 fw-bold mb-2"><?php echo get_the_title($team_post->ID); ?></h4>
                                            <p class="fs-6"><?php echo get_field('role', $team_post->ID); ?></p>
                                        </div>
										<?php if ( $is_modal && get_field( 'bio', $team_post->ID ) && get_field( 'bio', $team_post->ID ) != '' ) { ?>
                                            <button type="button" class="mt-2 ms-auto btn p-0 d-flex align-items-center justify-content-center"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#teamModal"
                                                    data-bs-name="<?php echo esc_attr( get_the_title( $team_post->ID ) ); ?>"
                                                    data-bs-role="<?php echo esc_attr( get_field( 'role', $team_post->ID ) ); ?>"
                                                    data-bs-bio="<?php echo esc_attr( get_field( 'bio', $team_post->ID ) ); ?>"
                                                    data-bs-image="<?php echo esc_attr( get_the_post_thumbnail_url( $team_post->ID, 'medium_large' ) ); ?>">
                                                <span class="visually-hidden">View Bio</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 17 17">
                                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                                                </svg>
                                                <?php //echo do_shortcode('[icon icon="chevron-right" size="xs" xclass="ms-3"]'); ?>
                                            </button>
                                        <?php } elseif ($is_public) {
											?>
                                            <a class="btn btn-arrow" href="<?php echo get_permalink($team_post); ?>"><?php _e('Learn More', 'theme'); ?><span class="visually-hidden"> <?php _e('about', 'theme'); ?><?php echo get_the_title($team_post); ?></span></a>
											<?php
										} ?>
                                    </div>
                                </div>
                            </div>

							<?php
						}
					}

					$card = ob_get_clean();

					if ( $layout === 'grid' ) {
						echo $card;
					} else {
						?>
                        <a href="#skip-<?php echo $the_block->getId(); ?>" class="visually-hidden-focusable btn btn-primary position-absolute z-2"><?php _e( 'Skip team member slider.', 'theme' ); ?></a>
                        <div class="team-inner --slider">
                            <div class="swiper">
                                <div class="swiper-wrapper"
                                     data-swiper-slides-per-view="<?php echo $cards_per_row; ?>"
                                     data-swiper-autoplay="<?php echo $autoplay; ?>"
                                     data-swiper-delay="<?php echo $delay; ?>"
                                     data-swiper-speed="<?php echo $speed; ?>"
                                     data-swiper-pagination="<?php echo $pagination; ?>"
                                     data-swiper-navigation="<?php echo $navigation; ?>"
                                     data-swiper-loop="<?php echo $loop; ?>"
                                ><?php echo( $layout === 'slider' ? $card : '' ) ?></div>
                            </div>
							<?php if ( $navigation === 'true' && ! $is_preview ) { ?>
                                <div class="swiper-navigation py-3">
                                    <button class="swiper-button-prev btn btn-primary me-3">
                                        <svg width="16" height="26" viewBox="0 0 16 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.9058 24L3 13.0942L13.9058 2.18833" stroke="currentColor" stroke-width="4"/>
                                        </svg>

                                    </button>
                                    <button class="swiper-button-next btn btn-primary">
                                        <svg width="16" height="26" viewBox="0 0 16 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.09416 2L13 12.9058L2.09416 23.8117" stroke="currentColor" stroke-width="4"/>
                                        </svg>

                                    </button>
                                </div>
							<?php } ?>
							<?php if ( $pagination === 'true' && ! $is_preview ) { ?>
                                <div class="swiper-pagination mx-auto">
                                </div>
							<?php } ?>
                        </div>
                        <div id="skip-<?php echo $the_block->getId(); ?>"></div>
						<?php
					}
					?>

                </div>
            </div>


        </div>
    </div>

	<?php
	wp_reset_postdata();

	$output = ob_get_clean();

	echo $the_block->renderSection( $output, 'basic' );

	if ( $is_modal ) {
		add_action( 'wp_footer', function () { ?>

            <div class="team-modal modal fade" id="teamModal" tabindex="-1" aria-labelledby="teamModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" data-bs-dismiss="modal" aria-label="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                    <path d="M30 10L10 30" stroke="#142E47" stroke-width="2" stroke-linecap="square" stroke-linejoin="round"/>
                                    <path d="M10 10L30 30" stroke="#142E47" stroke-width="2" stroke-linecap="square" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row mx-0">
                                <div class="col-12 col-lg-4 img-col pb-3 px-xl-5">
                                    <div class="modal--avatar-wrapper">
                                        <img src="" class="avatar img-fluid rounded"/>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-8 px-3 pe-md-5">
                                    <h5 class="h3 text-primary modal--team-name mb-1"></h5>
                                    <span class="modal--team-role"></span>
                                    <div class="modal--team-bio my-4"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

		<?php } );
	}

};

$render( $block, $is_preview, $content );