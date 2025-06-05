<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$render = function ( $block, $is_preview, $content ) {
	global $post;

	if ( ! $block ) {
		return;
	}

	$the_block = new Level\Block( $block );
	if ( $is_preview ) {
		$the_block->addClass( '--preview' );
	}

	$min_height = $the_block->getField( 'min_height' ) ? $the_block->getField( 'min_height' ) . 'px' : 'auto';
	if ( $min_height !== 'auto' ) {
		$the_block->addStyle( '--min-height:' . $min_height );
	} else {
		$the_block->addAttribute( [ 'data-aspect-ratio' => 'square' ] );
	}
	$layout = $the_block->getField( 'layout' );
	$the_block->addAttribute( [ 'data-layout' => $layout ] );

	$align = $the_block->getField( 'align' );

	$mobile = $the_block->getField( 'mobile' );
	$the_block->addAttribute( [ 'data-mobile' => $mobile ], 0 );

	if($layout === 'slider') {
		$mobile = 'grid';
	}

    //data-swiper-slides-per-view="<?php echo $the_block->getField('cards_per_row') ?: 3;"
    //swiperSlidesPerView
    $the_block->addAttribute( [ 'data-swiper-slides-per-view' => $the_block->getField( 'cards_per_row' ) ?: 3 ] );

	$innerBlocks = [
		[
			'lvl/cards-card',
		],
	];

	$allowedBlocks = [
		'lvl/cards-card',
	];

	$inner = '<InnerBlocks template="' . esc_attr( wp_json_encode( $innerBlocks ) ) . '" orientation="horizontal" templateLock="false" allowedBlocks="' . esc_attr( wp_json_encode( $allowedBlocks ) ) . '" />';

	$the_block->addStyle( '--card-count:' . ( $the_block->getField( 'cards_per_row' ) ?: 3 ) );

	ob_start(); ?>
	<?php if ( $layout === 'slider' && $is_preview ) {
        echo $the_block->previewNotice('info', 'Slider layout. This block will display as a slider on the front end.');
	} ?>
    <div class="cards row justify-content-<?php echo( $align ?: 'center' ) ?><?php echo( $mobile === 'slider' ? ' d-none d-md-flex' : '' ); ?>">
		<?php if ( $layout === 'slider' && ! $is_preview ) {
			?>
            <div class="swiper p-4">
                <div class="swiper-wrapper" data-swiper-slides-per-view="<?php echo $the_block->getField('cards_per_row') ?: 3; ?>">
					<?php echo $inner; ?>
                </div>
            </div>
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
            <div class="swiper-pagination"></div>
		<?php } else { ?>
			<?php echo $inner; ?>
		<?php } ?>
    </div>

	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection( $output );

};

$render( $block ?? false, $is_preview ?? false, $content ?? false );