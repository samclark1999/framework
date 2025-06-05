<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$render = function ( $block, $is_preview, $content ) {
	if ( ! $block ) {
		return;
	}

	$the_block = new Level\Block( $block );
	if ( $is_preview ) {
		$the_block->addClass( '--preview' );
	}

	$layout        = $the_block->getField( 'layout' );
	$mobile        = $the_block->getField( 'mobile_display_as' );
	$cards_per_row = $the_block->getField( 'cards_per_row' );

	$innerBlocks   = [
		[
			'lvl/gallery-card',
		],
	];
	$allowedBlocks = [ 'lvl/gallery-card' ];
	$inner         = '<InnerBlocks template="' . esc_attr( wp_json_encode( $innerBlocks ) ) . '" orientation="horizontal" templateLock="false" allowedBlocks="' . esc_attr( wp_json_encode( $allowedBlocks ) ) . '" />';

	$the_block->addAttribute( [ 'data-mobile' => $mobile ] );
	$the_block->addStyle( '--card-count:' . ( $cards_per_row ?: 3 ) );

	$stretch_width = $the_block->getField( 'slider_options:stretch_width' );
	if ( $stretch_width ) {
		$the_block->addClass( '--stretch-width' );
	}

	ob_start(); ?>
    <div class="gallery">
		<?php
		if ( $is_preview && $layout === 'slider' ) {
			echo $the_block->previewNotice( 'info', 'This will display as a slider on frontend.' );
		}
		?>
		<?php
		if ( $layout === 'slider' || $mobile === 'slider' && ! $is_preview ) {
			$the_block->addClass( 'px-4' );

			$autoplay   = $the_block->getField( 'slider_options:autoplay' ) ? 'true' : 'false';
			$delay      = $the_block->getField( 'slider_options:delay' );
			$speed      = $the_block->getField( 'slider_options:speed' );
			$pagination = $the_block->getField( 'slider_options:pagination' ) ? 'true' : 'false';
			$navigation = $the_block->getField( 'slider_options:navigation' ) ? 'true' : 'false';
			$loop       = $the_block->getField( 'slider_options:loop' ) ? 'true' : 'false';

			$mobile_visibility  = '';
			$desktop_visibility = '';
			if ( $layout === 'grid' && $mobile === 'slider' ) {
				$mobile_visibility  = ' d-md-none';
				$desktop_visibility = ' d-none d-md-flex';
				echo '<div class="gallery-inner --grid d-none d-md-flex">' . $inner . '</div>';
			}
			?>
            <div class="gallery-inner --slider<?php echo $mobile_visibility; ?>">
                <div class="swiper">
                    <div class="swiper-wrapper"
                         data-swiper-slides-per-view="<?php echo $cards_per_row; ?>"
                         data-swiper-autoplay="<?php echo $autoplay; ?>"
                         data-swiper-delay="<?php echo $delay; ?>"
                         data-swiper-speed="<?php echo $speed; ?>"
                         data-swiper-pagination="<?php echo $pagination; ?>"
                         data-swiper-navigation="<?php echo $navigation; ?>"
                         data-swiper-loop="<?php echo $loop; ?>"
                    ><?php echo( $layout === 'slider' ? $inner : '' ) ?></div>
                </div>
				<?php if ( $navigation === 'true' && ! $is_preview ) { ?>
                    <div class="swiper-button-next">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-compact-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z"/>
                        </svg>
                    </div>
                    <div class="swiper-button-prev">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-compact-left" viewBox="0 0 16 16">
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
			echo '<div class="gallery-inner --grid">' . $inner . '</div>';
		}

		?>
    </div>
	<?php
	$output = ob_get_clean();

	echo $the_block->renderSection( $output, ( $layout === 'slider' && $stretch_width ? 'basic' : 'full' ) );
};

$render( $block ?? false, $is_preview ?? false, $content ?? false );