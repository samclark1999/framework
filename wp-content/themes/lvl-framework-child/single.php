<?php global $post;
if (!defined('ABSPATH')) {
	exit;
}

$download_file = get_field('download_file');
$external_url = get_field('external_url');
$disable_page_view = get_field('disable_page_view');
$show_author = get_field('show_author') ?? false;
$show_date = get_field('show_date') ?? true;

if ($download_file && $disable_page_view) {
	wp_safe_redirect(wp_get_attachment_url($download_file));
	exit;
}

if ($external_url && $disable_page_view) {
	wp_redirect($external_url);
	exit;
}

$schema_type = match (get_post_type()) {
	'post' => 'BlogPosting',
	'event' => 'Event',
	'vendor' => 'Organization',
	'press-release' => 'NewsArticle',
	'video-podcast' => 'PodcastEpisode',
	default => 'WebPage',
};

$schema = [
		'@context'         => 'https://schema.org',
		'@type'            => $schema_type,
		'headline'         => get_the_title(),
		'datePublished'    => get_the_date('Y-m-d'),
		'dateModified'     => get_the_modified_date('Y-m-d'),
		'author'           => [
				'@type' => 'Person',
				'name'  => ($show_author ? get_the_author_meta('display_name') : 'Default User'),
		],
		'publisher'        => [
				'@type' => 'Organization',
				'name'  => get_bloginfo('name'),
				'logo'  => [
						'@type'  => 'ImageObject',
						'url'    => get_template_directory_uri() . '/dist/img/logo.svg',
						'width'  => 200,
						'height' => 82,
				],
		],
		'mainEntityOfPage' => [
				'@type' => 'WebPage',
				'@id'   => get_the_permalink(),
		],
];

$display_featured_image = false;
if (has_post_thumbnail()) {
	$schema['image'] = [
			'@type' => 'ImageObject',
	];

//    $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
//    if ($image) {
//        $image_width = $image[1];
//        $image_height = $image[2];
//        $schema['image']['url'] = $image[0];
//        $schema['image']['width'] = $image_width;
//        $schema['image']['height'] = $image_height;
//    }
}

add_action('wp_head', function () use ($schema) {
	echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
});

get_header();

if (have_posts()) :
	while (have_posts()) :
		the_post();

		if (is_singular()) :

			$id = 'resource_' . get_the_ID();
			$cats = get_the_terms($post, 'category');
			?>

			<section class="single-article">
			
				<article <?php post_class('type---post-single'); ?>>

					<div class="container">
						<?php if (has_post_thumbnail()) { ?>
							<?php
							$image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
							if ($image) {
								?>
								<div class="row mb-3">
									<div class="col-12 m-auto">
										<div class="featured-image my-3">
											<?php the_post_thumbnail('large', ['class' => 'img-fluid w-100']); ?>
										</div>
									</div>
								</div>
							<?php } ?>
						<?php } ?>
						<div class="row mb-3">
							<div class="col-12 col-lg-10 m-auto">
								<?php

								echo ($cats)? '<div class="d-flex flex-wrap gap-2 mb-3">' : '';

								foreach ($cats as $cat) {
									echo'<span class="d-inline-block lh-1 py-1 px-2 bg-primary rounded text-light border border-primary">' . $cat->name . '</span>';
								}

								echo ($cats)? '</div>' : '';
								
								?>
								<h1 class="post-title"><?php the_title(); ?></h1>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-12 col-lg-10 m-auto">
								<div class="meta gap-2">
									<?php
									if ($show_author) {
										$author_link = get_author_posts_url(get_the_author_meta('ID'));
										$author_name = get_the_author_meta('display_name');
										if ($author_link) {
											echo '<a href="' . $author_link . '">' . $author_name . '</a>';
										} else {
											// Fallback if author link is not available
											echo '<span>' . esc_html($author_name) . '</span>';
										}
									}
									if ($show_date) {
										?>
										<div class="date">
											<p>
												<?php echo get_the_date(); ?>
											</p>
										</div>
										<?php
									}
									?>
								</div>
							</div>
						</div>

						<?php
						if ($download_file) {
							?>
							<div class="row mb-5">
								<div class="col-12 col-lg-10 m-auto">
									<div class="py-3 px-5 bg-primary-subtle rounded border border-primary text-center" data-lvl-stretch-link="true">
										<h2 id="download-label" class="h5 text-body d-inline-block me-3">
											"<?php the_title(); ?>"
											<?php _e('is available for download.', 'theme'); ?>
										</h2>
										<a href="<?php echo wp_get_attachment_url($download_file); ?>" class="btn btn-primary" target="_blank" rel="nofollow" aria-labelledby="download-label">
											<?php _e('Download', 'theme'); ?>
										</a>
									</div>
								</div>
							</div>
							<?php
						} elseif ($external_url) {
							$is_local = strpos($external_url, get_site_url()) !== false;
							$message = $is_local ? __('is available to view.', 'theme') : __('is available on an external site.', 'theme');
							$cta = $is_local ? __('View', 'theme') : __('View', 'theme');
							?>
							<div class="row mb-5">
								<div class="col-12 col-lg-10 m-auto">
									<div class="py-3 px-5 bg-primary-subtle rounded border border-primary text-center" data-lvl-stretch-link="true">
										<h2 id="external-label" class="h5 text-body d-inline-block me-3">
											"<?php the_title(); ?>"
											<?php echo $message; ?>
										</h2>
										<a href="<?php echo $external_url; ?>" class="btn btn-primary" target="_blank" rel="nofollow" aria-labelledby="external-label">
											<?php echo $cta; ?>
										</a>
									</div>
								</div>
							</div>
							<?php
						}
						?>
					</div>
					<?php
					//                    if (has_blocks()) {
					//                        the_content();
					//                    } else {
					?>
					<div class="post-content container<?php echo(has_blocks() ? '' : ' no-blocks'); ?>">
						<div class="row">
							<div class="col-12 col-lg-10 m-auto">
								<?php
								$content = get_the_content();
								// remove img with alt="Image ID: ###"
								$content = preg_replace('/<img[^>]*alt="Image ID: \d+"[^>]*>/', '', $content);

								echo apply_filters('the_content', $content);

								?>
							</div>
						</div>
					</div>
					<?php
					//                    }
					?>
				</article>
			</section>
			<section class="share-post">
				<div class="container mb-5">
					<div class="row align-items-end">
						<div class="col-12 col-lg-10 m-auto">
							<?php echo get_template_part( 'includes/share' ); ?>
						</div>
					</div>
				</div>

			</section>

			<div class="container py-4">
				<div class="row">
					<div class="col-12">
						<div class="divider">
							<hr>
						</div>
					</div>
				</div>
			</div>

			<section class="related-posts my-4">

				<div class="container">
					<div class="row mb-5">
						<div class="col-12">
							<h3><strong><?php _e('Related', 'theme') ?></strong></h3>
						</div>
					</div>

					<div class="row">
						<div class="col-12">
							<div class="row justify-content-center">
								<?php

								$post_type = get_post_type();
								$related = [];
								$related_tax = [];
								$tax_try = [
										'category',
										'resource_type',
										'event_type',
								];

								foreach ($tax_try as $tax) {
									$args = [
											'post_type'           => $post_type,
											'post_status'         => 'publish',
											'posts_per_page'      => 3,
											'ignore_sticky_posts' => 1,
											'post__not_in'        => [get_the_ID(), ...$related],
											'fields'              => 'ids',
											'orderby'             => 'date',
											'order'               => 'DESC',
									];

									$categories = wp_get_post_terms(get_the_ID(), $tax, ['fields' => 'ids']);
									if(!is_wp_error($categories)) {
										$args['category__in'] = $categories;
									}

									$posts = new WP_Query($args);
									$posts = $posts->posts;

									$related = array_unique(array_merge($related, $posts));
									$related_tax[] = $tax;

									if (count($related) >= 3) {
										$related = array_slice($related, 0, 3);
										break;
									}
								}


								if (count($related) < 3) {
									unset($args['category__in']);
									$args['post_type'] = 'post';
									$posts = new WP_Query($args);
									$posts = $posts->posts;

									$related = array_unique(array_merge($related, $posts));

									$related = array_slice($related, 0, 3);
								}

								?>

								<?php if ($related) :
									// extract ids from wp_posts
									$related_ids = '["' . implode('","', $related) . '"]';

									$related_layout = '<!-- wp:lvl/post-listing {"name":"lvl/post-listing","data":{"field_6596f612762a3":["post"],"field_post_listing_6596f523762a4":"3","field_66314d9ec6bd0":' . $related_ids . ',"post_listing_pagination":"none","field_post_listing_randomize":"0","field_6596f523762a2":"0","field_post_listing_show_result_count":"0","field_post_listing_display_options_key":["meta","excerpt","featured_image","link"],"field_post_listing_link_text":"Read More","field_post_listing_card_layout":"column","field_677ab0af7878d":"0","field_post_listing_card_per_row":"3","field_post_listing_card_min_height":"0"},"mode":"preview","style":{},"bs":{"theme":""}} /-->';

									echo apply_filters('the_content', $related_layout);

									?>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

			</section>

		<?php else :

			the_content();

		endif;
	endwhile;
endif;

get_footer();