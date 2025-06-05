<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {
    if (!$block) return;

    $the_block = new Level\Block($block);
    
	if ($is_preview) {
        $the_block->addClass('--preview');
    }

    $innerBlocks = [
        [
            'lvl/timeline-event',
        ],
    ];

    $allowedBlocks = ['lvl/timeline-event'];

    $inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="false" allowedBlocks="' . esc_attr(wp_json_encode($allowedBlocks)) . '" />';

    ob_start(); ?>
	
	<div class="row mx-0">

		<div class="col-12 col-lg-4 p-0">
			<div class="years"></div>
			<div class="swiper-button-next" role="button">
				<svg xmlns="http://www.w3.org/2000/svg" width="25" height="15" viewBox="0 0 25 15" fill="none"><path d="M2 2L12.5 12.5L23 2" stroke="currentColor" stroke-width="3"/></svg>
			</div>
		</div>
		
		<div class="col-12 col-lg-8 p-0 d-flex">
			<div class="events" data-bs-theme="light">
			
			<?php echo $inner; ?>

			</div>
		</div>
	</div>

    <?php

    $output = ob_get_clean();

    echo $the_block->renderSection($output, true);

};

$render($block ?? false, $is_preview ?? false, $content ?? false);