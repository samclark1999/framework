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

	$cards_per_row = ( $is_preview ? 3 : 1 ); //$the_block->getField( 'cards_per_row' );
	$the_block->addStyle( '--card-count:' . $cards_per_row );

	$innerBlocks   = [
		[
			'lvl/gallery-card',
		],
	];
	$allowedBlocks = [ 'lvl/gallery-card' ];
	$inner         = '<InnerBlocks template="' . esc_attr( wp_json_encode( $innerBlocks ) ) . '" orientation="horizontal" templateLock="false" allowedBlocks="' . esc_attr( wp_json_encode( $allowedBlocks ) ) . '" />';


	$stretch_width = $the_block->getField( 'slider_options:stretch_width' );
	if ( $stretch_width ) {
		$the_block->addClass( '--stretch-width' );
	}

	ob_start(); ?>
    <div class="gallery-slideshow">
		<?php
		if ( $is_preview ) {
			echo $the_block->previewNotice( 'info', 'This will display as a single slide slider on frontend.' );
		}
		?>
		<?php
		if ( ! $is_preview ) {
			$autoplay   = $the_block->getField( 'slider_options:autoplay' ) ? 'true' : 'false';
			$delay      = $the_block->getField( 'slider_options:delay' );
			$speed      = $the_block->getField( 'slider_options:speed' );
			$pagination = $the_block->getField( 'slider_options:pagination' ) ? 'true' : 'false';
			$navigation = $the_block->getField( 'slider_options:navigation' ) ? 'true' : 'false';
			$loop       = $the_block->getField( 'slider_options:loop' ) ? 'true' : 'false';
			$transition = $the_block->getField( 'slider_options:transition' ) ?: 'fade';

			?>
            <div class="gallery-inner --slider">
                <div class="gallery-header d-flex justify-content-between align-items-center<?php echo( $stretch_width ? ' container' : '' ); ?>">
                    <div class="gallery-count h5">
                        <span><?php _e( 'Gallery', 'theme' ); ?></span> <span class="text-primary"><span class="current">01</span> / <span class="total">01</span></span>
                    </div>
					<?php if ( $navigation === 'true' && ! $is_preview ) { ?>
                        <div class="slide-navigation h5">
                            <div class="swiper-button-next" role="button">
								<?php _e( 'Next', 'theme' ); ?>
                            </div>
                            /
                            <div class="swiper-button-prev" role="button">
								<?php _e( 'Prev', 'theme' ); ?>
                            </div>
                        </div>
					<?php } ?>
                </div>
                <div class="swiper">
                    <div class="swiper-wrapper"
                         data-swiper-slides-per-view="<?php echo $cards_per_row; ?>"
                         data-swiper-autoplay="<?php echo $autoplay; ?>"
                         data-swiper-delay="<?php echo $delay; ?>"
                         data-swiper-speed="<?php echo $speed; ?>"
                         data-swiper-pagination="<?php echo $pagination; ?>"
                         data-swiper-navigation="<?php echo $navigation; ?>"
                         data-swiper-loop="<?php echo $loop; ?>"
                         data-swiper-effect="<?php echo $transition; ?>"
                    ><?php echo $inner; ?></div>
                </div>
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

	echo $the_block->renderSection( $output, ( $stretch_width ? 'basic' : 'full' ) );
};

$render( $block ?? false, $is_preview ?? false, $content ?? false );