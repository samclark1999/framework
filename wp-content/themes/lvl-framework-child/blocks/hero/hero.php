<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$render = function ( $block, $is_preview, $content ) {

	$the_block = new Level\Block( $block );
	if ( $is_preview ) {
		$the_block->addClass( '--preview' );
	}

	$is_overlay = $the_block->getField( 'overlay' ) ?? true;
	if ( $is_overlay ) {
		$the_block->addClass( '--overlay' );
	}

	$background = $the_block->getField( 'background' ) ?: [];
	$type       = $background['type'] ?? 'default';

	$speed      = $the_block->getField( 'slider_options:speed' );
	$autoplay   = $the_block->getField( 'slider_options:autoplay' ) ? 'true' : 'false';
	$delay      = $the_block->getField( 'slider_options:delay' );
	$pagination = $the_block->getField( 'slider_options:pagination' ) ? 'true' : 'false';
	$navigation = $the_block->getField( 'slider_options:navigation' ) ? 'true' : 'false';
	$loop       = $the_block->getField( 'slider_options:loop' ) ? 'true' : 'false';

	$hero_min_height = $the_block->getField( 'min_height' );
	if ( $hero_min_height ) {
		$the_block->addStyle( [ '--hero-min-height:' . $hero_min_height . 'px' ] );
		$the_block->addAttribute( [ 'height' => $hero_min_height ] );
	}


	$innerBlocks = [];
	$inner       = '<InnerBlocks 
    template="' . esc_attr( wp_json_encode( $innerBlocks ) ) . '" 
    templateLock="false" 
    />';

	ob_start(); ?>
    <div class="hero-inner">
		<?php echo $inner; ?>
    </div>
    <div class="hero-annotation-display p-3"></div>
	<?php
	if ( $type === 'image' ) {
		$images = $background['images'] ?? [];
		if ( $images ) {
			$the_block->addClass( '--image-bg' );
			if ( $is_preview ) {
				$the_block->addStyle( [ 'background-image: url(' . $images[0]['image'] . ')' ] );
			} else {
				?>
                <div class="hero-images">
                    <div class="swiper">
                        <div class="swiper-wrapper"
                             data-swiper-speed="<?php echo $speed; ?>"
                             data-swiper-autoplay="<?php echo $autoplay; ?>"
                             data-swiper-delay="<?php echo $delay; ?>"
                             data-swiper-pagination="<?php echo $pagination; ?>"
                             data-swiper-navigation="<?php echo $navigation; ?>"
                             data-swiper-loop="<?php echo $loop; ?>"
                        >
							<?php
							foreach ( $images as $image ) {
								?>
                                <div class="swiper-slide hero-image bg-cover" style="background-image: url(<?php echo( $image['image'] ?? '1' ); ?>)">
									<?php
									if ( ($image['annotation']['title']??'') !== '' ) {
										?>
                                        <div class="hero-annotation"><a href="<?php echo $image['annotation']['url']; ?>" target="_blank" class="annotation"><?php echo $image['annotation']['title']; ?></a></div>
										<?php
									}
									?>
                                </div>
								<?php
							}
							?>
                        </div>
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
                </div>
				<?php
			}
		}
	} elseif ( $type === 'video' || $type === 'file' ) {
		$video = $background['video'] ?? [];
		if ( $type === 'file' ) {
			$video = $background['file']['url'] ?? '';
		}
		if ( $video ) {
			$the_block->addClass( '--video-bg' );
			?>
            <div class="video-wrapper" data-src="<?php echo $video; ?>"></div>
			<?php
		}
	} else {
		?>
        <div class="hero-default"></div>
		<?php
	}
	?>
	<?php $output = ob_get_clean();

	echo $the_block->renderSection( $output, 'full' );

};

$render( $block ?? false, $is_preview ?? false, $content ?? false );