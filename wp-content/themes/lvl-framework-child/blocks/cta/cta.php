<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$render = function ( $block, $is_preview, $content ) {

	$the_block = new Level\Block( $block );

	$background_image = $the_block->getField( 'background' );
	if ( $background_image ) {
		$the_block->addStyle( [
			'background: url(' . wp_get_attachment_url( $background_image ) . ') no-repeat center center / cover;',
		], 10 );
	}

	$the_block->addClass( 'p-2 p-md-3' );

	$inner = '<InnerBlocks templateLock="false" />';

	ob_start(); ?>
    <div class="cta-wrapper" style="<?php echo $the_block->getStyle(10); ?>">
        <div class="cta-wrapper p-4 p-md-5 p-lg-7">
            <div class="cta-content">
				<?php echo $inner; ?>
            </div>
        </div>
    </div>
	<?php
	$output = ob_get_clean();

	echo $the_block->renderSection( $output, 'basic' );

};

$render( $block, $is_preview, $content );