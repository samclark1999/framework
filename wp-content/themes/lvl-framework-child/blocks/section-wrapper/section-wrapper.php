<?php if (!defined('ABSPATH')) exit;

$render = function ( $block, $is_preview, $content ) {

	$the_block = new Level\Block( $block );

	$inner = '<InnerBlocks templateLock="false" />';

	ob_start(); ?>

    <div class="inner-wrapper">

		<?php
		if ( $is_preview ) {
//			echo $the_block->previewNotice( 'info', 'This is a section wrapper block. It is used to wrap other blocks and provide extra styling options!' );
		}

		echo $inner; ?>

    </div>

	<?php

	$output = ob_get_clean();

//    if( str_contains( $content, 'wp-block-columns' ) ) {
//        echo $the_block->renderSection($output, 'container');
//        return;
//    }

	echo $the_block->renderSection( $output );

};
$render( $block, $is_preview, $content );