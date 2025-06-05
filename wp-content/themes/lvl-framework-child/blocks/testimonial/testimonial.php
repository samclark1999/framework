<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {

	$the_block = new Level\Block($block);
	$testimonial = $the_block->getField('testimonial');

	if ($testimonial) {
		$image = get_post_thumbnail_id($testimonial);
	}

	$innerBlocks = [
			[
					'core/heading',
					[
							'level'   => 4,
							'content' => 'Tempore incidunt omnis quidem eius',
					],
			],
			[
					'core/paragraph',
					[
							'content' => 'Quo similique voluptates illo. Ut cumque quibusdam quia. Quibusdam et officiis dolorem dignissimos alias. Iusto ut voluptates sint animi deleniti sequi voluptatem corporis. Quia voluptatum fuga. Occaecati est nam ab in aspernatur molestias vero earum.',
					],
			],
	];

//    $allowedBlocks = ['core/heading', 'core/paragraph', 'core/button', 'core/spacer', 'core/separator'];

	$inner = '<InnerBlocks template="' . esc_attr(wp_json_encode($innerBlocks)) . '" templateLock="false" />';

	ob_start(); ?>

	<div class="slide row m-0">

		<?php if ($image) { ?>

			<div class="col-12 col-md-auto position-relative py-4 py-md-5 d-none d-md-block">

				<?php echo wp_get_attachment_image($image['ID'], 'medium', false, ['class' => 'slide-image img-fluid']); ?>

			</div>

		<?php } ?>
		<div class="col-12 <?php echo ($image) ? 'col-md' : ''; ?> py-4 py-md-5">

			<?php if (!$testimonial) {

				echo $inner;

			} else {
				$display_options = get_field('testimonial_display_options');

				$is_image = true;
				$is_name = true;
				$is_title = false;
				$is_company = false;

				if (!is_null($display_options)) {
					$is_image = in_array('display_image', $display_options, true) && get_the_post_thumbnail($testimonial);
					$is_name = in_array('display_name', $display_options, true);
					$is_title = in_array('display_position', $display_options, true);
					$is_company = in_array('display_company', $display_options, true);
				}

				$role = [];
				$name = get_field('quote_author', $testimonial) ?: '';
				if (get_field('quote_author_title', $testimonial) && $is_title) {
					$role[] = get_field('quote_author_title', $testimonial);
				}
				if (get_field('quote_author_co', $testimonial) && $is_company) {
					$role[] = get_field('quote_author_co', $testimonial);
				}
				$quote = get_field('quote_content', $testimonial) ?: '';
				?>
				<div data-component="core:columns" data-bs-theme="" class="wp-block-columns is-layout-flex wp-block-columns-is-layout-flex" style="gap: 2em var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
					<?php if ($is_image) { ?>
						<div data-component="core:column" data-bs-theme="" class="wp-block-column is-style-default is-layout-flow wp-block-column-is-layout-flow" style="flex-basis:108px">
							<figure data-component="core:image" data-bs-theme="" class="wp-block-image size-full is-resized">
								<img decoding="async" width="108" height="108" src="<?php echo esc_url(get_the_post_thumbnail_url($testimonial, 'thumbnail')); ?>" alt="<?php echo esc_attr($name); ?>" class="img-fluid rounded" style="--block-height:108px;object-fit:cover;width:108px;height:108px">
							</figure>
						</div>
					<?php } ?>

					<div data-component="core:column" data-bs-theme="" class="wp-block-column is-style-default is-layout-flow wp-block-column-is-layout-flow">
						<?php if (!empty($quote)) { ?>
							<p><?php echo esc_html($quote); ?></p>
						<?php } ?>
						<?php if ($is_name) { ?>
							<p><strong><?php echo esc_html($name); ?></strong></p>
						<?php } ?>
						<?php if ($is_title && !empty($role)) { ?>
							<p class="has-body-small-font-size mt-1"><?php echo esc_html(implode(', ', $role)); ?></p>
						<?php } ?>
					</div>
				</div>
			<?php } ?>

		</div>

	</div>

	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection($output, 'basic');

};

$render($block, $is_preview, $content);