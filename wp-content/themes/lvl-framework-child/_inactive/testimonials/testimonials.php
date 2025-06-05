<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$render = function ( $block, $is_preview, $content ) {

	$the_block = new Level\Block( $block );
	$the_block->addClass( 'py-3' );

	$testimonials = $the_block->getField( 'testimonials' )?: [];

	ob_start(); ?>

    <div class="testimonials mx-lg-5">
        <div class="testimonials--wrapper">
			<?php
			if ( $is_preview && empty( $testimonials ) ) {
				echo $the_block->previewNotice('warning', 'Click to select Block to pick testimonials from editor sidebar.', 'NOTICE: No Testimonials selected.');
			}
			?>

			<?php foreach ( $testimonials as $testimonial ) : $fields = get_fields( $testimonial->ID ); ?>

                <div class="testimonial shadow-sm bg-white rounded" data-bs-theme="light">
                    <div class="row">
						<?php
						if ( has_post_thumbnail( $testimonial->ID ) ) {
							$image = get_post_thumbnail_id( $testimonial->ID );
							?>
                            <div class="col-12 col-md-3 col-lg-5 order-1 order-md-last">
                                <?php echo wp_get_attachment_image($image, 'large', false, ['class' => 'object-fit--cover']) ?>
                            </div>
							<?php
						}

						?>
                        <div class="col order-2">
                            <figure class="p-3 p-lg-4">
                                <blockquote>
                                    <p><?php echo $fields['quote_content']; ?></p>
                                </blockquote>
								<?php if ( $fields['quote_author'] || $fields['quote_author_title'] || $fields['quote_author_co'] ): ?>
                                    <figcaption class="mx-auto mt-3">
                                        &mdash;
										<?php if ( $fields['quote_author'] ) :
											echo '<span class="author">' . $fields['quote_author'] . '</span>';
										endif;
										if ( $fields['quote_author_title'] ) :
											echo '<span class="title">' . $fields['quote_author_title'] . '</span>';
										endif;
										if ( $fields['quote_author_co'] ):
											echo '<span class="company">' . $fields['quote_author_co'] . '</span>';
										endif; ?>
                                        &mdash;
                                    </figcaption>
								<?php endif; ?>
                            </figure>
                        </div>
                    </div>
                </div>

			<?php endforeach; ?>
        </div>
        <div class="row">
            <div class="col">
                <div class="testimonials--navigation align-items-center d-flex justify-content-between py-4 px-3 px-lg-4"></div>
            </div>
        </div>
    </div>

	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection( $output );

};

$render( $block, $is_preview, $content );