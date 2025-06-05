<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$render = function ( $block, $is_preview, $content ) {

	$the_block = new Level\Block( $block );
	if ( $is_preview ) {
		$the_block->addClass( '--preview' );
	}

	$speed      = $the_block->getField( 'slider_options:speed' );
	$autoplay   = $the_block->getField( 'slider_options:autoplay' ) ? 'true' : 'false';
	$delay      = $the_block->getField( 'slider_options:delay' );
	$pagination = $the_block->getField( 'slider_options:pagination' ) ? 'true' : 'false';
	$navigation = $the_block->getField( 'slider_options:navigation' ) ? 'true' : 'false';
	$loop       = $the_block->getField( 'slider_options:loop' ) ? 'true' : 'false';

	$banner_showcase_min_height = $the_block->getField( 'min_height' );
	if ( $banner_showcase_min_height ) {
		$the_block->addStyle( [ '--banner-showcase-min-height:' . $banner_showcase_min_height . 'px' ] );
		$the_block->addAttribute( [ 'height' => $banner_showcase_min_height ] );
	}

	ob_start(); ?>
	<?php
	$banners = $the_block->getField( 'banners' );
	if ( $banners ) {
		?>
        <div class="<?php echo( $is_preview ? '' : 'banner-showcase-banners' ) ?>">
			<?php
            ob_start();
			foreach ( $banners as $banner_id ) {

				$schedule_start = get_field( 'start', $banner_id ); //$the_block->getField( 'schedule:start' );
				$schedule_end   = get_field( 'end', $banner_id ); //$the_block->getField( 'schedule:end' );

				$now             = current_datetime(); // set to wp configured timezone
				$is_preview_hero = $is_preview;
				$status_note     = 'Always visible';
				$is_out_of_range = false;

				$timezone = wp_timezone(); // wp configured timezone

				if ( ( $schedule_start && $schedule_end ) ) {
					$start = new DateTime( $schedule_start, $timezone );
					$end   = new DateTime( $schedule_end, $timezone );

					$status_note = 'Schedule: ' . $start->format( 'Y-m-d H:i:s' ) . ' - ' . $end->format( 'Y-m-d H:i:s' );

					if ( $now < $start || $now > $end ) {
						$is_out_of_range = true;
						if ( ! $is_preview_hero ) {
							continue;
						}
					}
				} elseif ( $schedule_start ) {
					$start = new DateTime( $schedule_start, $timezone );

					$status_note = 'Schedule: ' . $start->format( 'Y-m-d H:i:s' ) . ' - ...';
					if ( $now < $start ) {
						$is_out_of_range = true;
						if ( ! $is_preview_hero ) {
							continue;
						}
					}
				} elseif ( $schedule_end ) {
					$end = new DateTime( $schedule_end, $timezone );

					$status_note = 'Schedule: ... - ' . $end->format( 'Y-m-d H:i:s' );
					if ( $now > $end ) {
						$is_out_of_range = true;
						if ( ! $is_preview_hero ) {
							continue;
						}
					}
				}

				if ( $is_preview ) {
					?>
                    <div class="position-absolute z-1 d-block text-body px-3 py-1 small bg-info-subtle m-2 rounded border border-info">
						<?php echo $status_note; ?><?php if ( $is_out_of_range ) { ?> <span class="fw-bold"> - OUT OF RANGE</span><?php } ?>
                    </div>
					<?php
				}

				echo apply_filters( 'the_content', get_the_content( null, null, $banner_id ) );
			}

            $banners_output = ob_get_clean();
			?>
        </div>
        <div class="swiper">
            <div class="swiper-wrapper"
                 data-swiper-speed="<?php echo $speed; ?>"
                 data-swiper-autoplay="<?php echo $autoplay; ?>"
                 data-swiper-delay="<?php echo $delay; ?>"
                 data-swiper-pagination="<?php echo $pagination; ?>"
                 data-swiper-navigation="<?php echo $navigation; ?>"
                 data-swiper-loop="<?php echo $loop; ?>"
            ><?php echo $banners_output; ?></div>
			<?php if ( $navigation === 'true' && ! $is_preview ) { ?>
                <div class="swiper-button-next" tabindex="0">
                    <span class="visually-hidden">Next slide</span>
                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="16" height="16" fill="currentColor" class="bi bi-chevron-compact-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z"/>
                    </svg>
                </div>
                <div class="swiper-button-prev" tabindex="0">
                    <span class="visually-hidden">Previous slide</span>
                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="16" height="16" fill="currentColor" class="bi bi-chevron-compact-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M9.224 1.553a.5.5 0 0 1 .223.67L6.56 8l2.888 5.776a.5.5 0 1 1-.894.448l-3-6a.5.5 0 0 1 0-.448l3-6a.5.5 0 0 1 .67-.223z"/>
                    </svg>
                </div>
			<?php } ?>
			<?php if ( $pagination === 'true' && ! $is_preview ) { ?>
                <div class="swiper-pagination mx-auto">
                </div>
			<?php } ?>
        </div>

		<?php
	} else {
		if ( $is_preview ) {
			echo $the_block->previewNotice( 'warning', 'No banners selected yet. Select this block and choose banners.' );
		} elseif ( is_user_logged_in() ) {
			echo $the_block->previewNotice( 'warning', 'Empty Banner Showcase Block. No banners selected yet. [ONLY VISIBLE TO LOGGED-IN USERS]' );
		}
	}
	?>
	<?php $output = ob_get_clean();

	echo $the_block->renderSection( $output, 'basic' );

};

$render( $block ?? false, $is_preview ?? false, $content ?? false );