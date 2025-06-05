<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @throws Exception
 */
$render = function ( $block, $is_preview, $content ) {

	$the_block = new Level\Block( $block );

	$is_overlay = $the_block->getField( 'overlay' );
	if ( $is_overlay ) {
		$the_block->addClass( '--overlay' );
	}

//	$schedule_start = get_field('schedule:start', get_the_ID()); //$the_block->getField( 'schedule:start' );
//	$schedule_end   = get_field('schedule:end'); //$the_block->getField( 'schedule:end' );
//
//	$now             = current_datetime(); // set to wp configured timezone
	$is_preview_hero = is_admin();
	$status_note     = 'Always visible';
	$is_out_of_range = false;
//
//	$timezone = wp_timezone(); // wp configured timezone
//
//	if ( $schedule_start && $schedule_end ) {
//		$start = new DateTime( $schedule_start, $timezone );
//		$end   = new DateTime( $schedule_end, $timezone );
//
//		$status_note = 'Schedule: ' . $start->format( 'Y-m-d H:i:s' ) . ' - ' . $end->format( 'Y-m-d H:i:s' );
//
//		if ( $now < $start || $now > $end ) {
//			$is_out_of_range = true;
//			if ( ! $is_preview_hero ) {
//				return;
//			}
//		}
//	} elseif ( $schedule_start ) {
//		$start = new DateTime( $schedule_start, $timezone );
//
//		$status_note = 'Schedule: ' . $start->format( 'Y-m-d H:i:s' ) . ' - ...';
//		if ( $now < $start ) {
//			$is_out_of_range = true;
//			if ( ! $is_preview_hero ) {
//				return;
//			}
//		}
//	} elseif ( $schedule_end ) {
//		$end = new DateTime( $schedule_end, $timezone );
//
//		$status_note = 'Schedule: ... - ' . $end->format( 'Y-m-d H:i:s' );
//		if ( $now > $end ) {
//			$is_out_of_range = true;
//			if ( ! $is_preview_hero ) {
//				return;
//			}
//		}
//	}

	$max_content_width = $the_block->getField( 'max_width' );
	if ( $max_content_width ) {
		$the_block->addStyle( [ '--lg-max-width:' . $max_content_width . '%' ], 'column' );
	}
	$content_alignment = $the_block->getField( 'alignment' );
	$alignment_class   = '';
	if ( $content_alignment ) {
		$alignment_class = ( ' m' . ( ! str_starts_with( $content_alignment, 'c' ) ? substr( $content_alignment, 0, 1 ) : 'x' ) . '-auto' );
	}

	$banner_min_height = $the_block->getField( 'min_height' );
	if ( $banner_min_height ) {
		$the_block->addStyle( [ '--banner-min-height:' . $banner_min_height . 'px' ] );
//        $the_block->addAttribute(['height' => $banner_min_height]);
	}

	$images = [
		'desktop' => [ 'size' => 'full', 'class' => 'd-none d-lg-block' ],
		'tablet'  => [ 'size' => 'large', 'class' => 'd-none d-md-block d-lg-none' ],
		'mobile'  => [ 'size' => 'medium_large', 'class' => 'd-md-none' ],
	];

	$img = '';
	foreach ( $images as $type => $value ) {
		$image = $the_block->getField( 'background_image:' . $type );
		if ( ! $image && $type == 'tablet' && ( $the_block->getField( 'background_image:tablet_inherit_image' ) ?? true ) ) {
			$image = $the_block->getField( 'background_image:desktop' );
		}
		if ( ! $image && $type == 'mobile' && ( $the_block->getField( 'background_image:mobile_inherit_image' ) ?? true ) ) {
			$image = $the_block->getField( 'background_image:tablet' ) ?: $the_block->getField( 'background_image:desktop' );
		}
		if ( $image ) {
			$img .= wp_get_attachment_image( $image, $value['size'], false, [ 'style' => 'width: 100%;height:100%;display:none;', 'class' => 'banner-background ' . $value['class'], 'loading' => false ] );
		}
	}

	$background_alignment = $the_block->getField( 'background_image:alignment' );
	if ( $background_alignment ) {
		$the_block->addStyle( [ '--background-image-alignment:' . $background_alignment ] );
	}

	$background_fit = $the_block->getField( 'background_image:fit' );
	if ( $background_fit ) {
		$the_block->addStyle( [ '--background-image-fit:' . $background_fit ] );
	}

	$full_banner_link = $the_block->getField( 'full_banner_link' );
	$stretch_link     = '';
	if ( $full_banner_link ) {
		$stretch_link = '<a href="' . $full_banner_link['url'] . '" class="visually-hidden-focusable">' . $full_banner_link['url'] . '</a>';
		$the_block->addAttribute( [ 'data-lvl-stretch-link' => 'true' ] );
		$the_block->addClass( 'linked-no-style' );
	}

	$innerBlocks = [
		array(
			'core/post-title',
			array(
				'fontSize' => 'h1',
			),
			array(),
		),
		[
			'core/paragraph',
			[
				'content'  => 'Tempore incidunt quae illo fuga voluptatibus tenetur et dignissimos soluta.',
				'fontSize' => 'h3',
			],
		],
		[
			'core/buttons',
			[],
			[
				[
					'core/button',
					[
						'text'     => 'Call to Action',
						'url'      => '',
						'fontSize' => 'h3',
					],
				],
			],
		],
	];

	$allowedBlocks = [
		'core/image',
		'core/heading',
		'core/paragraph',
		'core/buttons',
		'core/spacer',
		'core/separator',
	];

//	$inner = '<InnerBlocks
//    template="' . esc_attr( wp_json_encode( $innerBlocks ) ) . '"
//    templateLock="false"
    $inner = '<InnerBlocks templateLock="false" />';
	//allowedBlocks="' . esc_attr(wp_json_encode($allowedBlocks)) . '"

	ob_start(); ?>
	<?php //echo( $is_preview_hero && ! $is_preview ? '<pre class="banner-schedule d-inline-block  p-2 small text-dark bg-light border shadow-sm">' . $status_note . '</pre>' : '' ); ?>
    <div class="banner-wrapper<?php //echo( $is_preview_hero && ! $is_preview && $is_out_of_range ? ' opacity-25' : '' ) ?>">
		<?php echo $stretch_link; ?>
		<?php echo $img; ?>
        <div class="banner-content--wrapper container <?php echo( get_field( 'is_padding_disabled' ) ? 'py-0' : 'py-lg-5 py-4' ); ?>">
            <div class="row">
                <div class="col">
                    <div class="banner-content<?php echo $alignment_class; ?>" <?php echo $the_block->renderStyle( 'column' ); ?>>
						<?php echo $inner; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection( $output, 'basic' );

};

$render( $block ?? false, $is_preview ?? false, $content ?? false );