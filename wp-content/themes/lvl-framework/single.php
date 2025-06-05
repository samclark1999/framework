<?php global $post;
if (!defined('ABSPATH')) {
	exit;
}

$download_file = get_field('download_file');
$external_url = get_field('external_url');
$disable_page_view = get_field('disable_page_view');
$show_author = get_field('show_author') ?? true;
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
			$cat = get_the_terms($post, 'category');
			?>

			<section class="single-article" style="padding-top: var(--wp--preset--spacing--90);">

				<article <?php post_class('type---post-single'); ?>>

					<div class="container">
						<div class="row mb-5">
							<?php if ($display_featured_image && has_post_thumbnail()) { ?>
								<?php
								$image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
								if ($image) {
									?>
									<div class="col-12 col-lg-10 col-xl-8 m-auto">
										<div class="featured-image my-3">
											<?php the_post_thumbnail('large', ['class' => 'img-fluid w-100']); ?>
										</div>
									</div>
								<?php } ?>
							<?php } ?>
						</div>
						<div class="row mb-4">
							<div class="col-12 col-lg-10 col-xl-8 m-auto">
								<?php
								echo($cat ? '<span class="d-inline-block lh-1 py-1 px-2 rounded text-secondary border border-secondary">' . $cat[0]->name . '</span>' : '');
								?>
								<h1 class="post-title"><?php the_title(); ?></h1>
							</div>
						</div>
						<div class="row mb-5">
							<div class="col-12 col-lg-10 col-xl-8 m-auto">
								<div class="meta gap-2">
									<?php
									if ($show_author) {
										$author_link = get_author_posts_url(get_the_author_meta('ID'));
										$author_name = get_the_author_meta('display_name');
										if($author_link){
											echo '<a href="' . $author_link . '">' . $author_name . '</a>';
										} else {
											// Fallback if author link is not available
											echo '<span>' . esc_html($author_name) . '</span>';
										}
									}
									if ($show_date) {
										?>
										<div class="date">
											<p class="mb-0">
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
								<div class="col-12 col-lg-10 col-xl-8 m-auto">
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
								<div class="col-12 col-lg-10 col-xl-8 m-auto">
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
							<div class="col-12 col-lg-10 col-xl-8 m-auto">
								<?php
								$content = get_the_content();
								// remove img with alt="Image ID: ###"
								$content = preg_replace('/<img[^>]*alt="Image ID: \d+"[^>]*>/', '', $content);

								echo apply_filters('the_content', $content);

								// the_content();

								?>
							</div>
						</div>
					</div>
					<?php
					//                    }
					?>
					<?php  echo get_template_part( 'includes/share' );
					?>

				</article>

			</section>

			<section class="share-post">

				<div class="container mb-5">
					<div class="row align-items-end">
						<div class="col-12 col-lg-10 col-xl-8 m-auto">
							<h5 class="mb-0"><?php _e('Share'); ?>:</h5>
							<?php $content = strip_tags(strip_shortcodes(get_the_content()));
							//                            $excerpt = lvl_base_get_excerpt_by_word_count(trim($content), 45);
							$excerpt = get_the_excerpt();
							?>
							<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo rawurlencode(get_the_permalink()); ?>&title=<?php echo rawurlencode(get_the_title()); ?>" target="_blank" rel="noopener noreferrer">
								<svg>
									<use xlink:href="#linkedin"></use>
								</svg>
								<span class="visually-hidden">Linkedin</span>
							</a>
							<a href="https://twitter.com/intent/tweet?text=<?php echo rawurlencode($excerpt); ?>&amp;url=<?php echo rawurlencode(get_the_permalink()); ?>" target="_blank" rel="noopener noreferrer">
								<svg>
									<use xlink:href="#twitter"></use>
								</svg>
								<span class="visually-hidden">Twitter</span>
							</a>
							<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode(get_the_permalink()); ?>" target="_blank" rel="noopener noreferrer">
								<svg>
									<use xlink:href="#facebook"></use>
								</svg>
								<span class="visually-hidden">Facebook</span>
							</a>
							<a href="mailto:?subject=Check%20this%20out%20&amp;body=<?php echo rawurlencode('Here\'s the link: ' . get_the_permalink()); ?>" target="_blank" rel="noopener noreferrer">
								<svg>
									<use xlink:href="#mail"></use>
								</svg>
								<span class="visually-hidden">E-mail</span>
							</a>
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
										'vendor_category',
										'vendor_name',
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

								<?php if ($related) : ?>

									<?php foreach ($related as $post) :
										$cat = get_the_terms($post, 'category');
										$type = get_the_terms($post, 'resource_type');
										$event_type = get_the_terms($post, 'event_type');
										?>

										<div class="col-12 col-md-4 --col-lg-3 d-flex">
											<article id="resource-<?php echo $post; ?>" class="d-flex flex-column border shadow-sm overflow-hidden w-100" data-lvl-stretch-link="true">
												<?php
												$default_img = get_field('default_card_img', 'options');

												if (wp_get_attachment_image(get_post_thumbnail_id($post))) {
													?>
													<figure class="card-image-banner mb-0" style="overflow:hidden;width: 100%; height:150px;">
														<?php echo wp_get_attachment_image(get_post_thumbnail_id($post), 'medium_large', false, ["class" => "object-fit--cover"]); ?>
													</figure>
													<?php
												} elseif ($default_img) {
													?>
													<figure class="card-image-banner mb-0" style="overflow:hidden;width: 100%; height:150px;">
														<?php echo wp_get_attachment_image($default_img, 'medium_large', false, ["class" => "object-fit--cover"]); ?>
													</figure>
													<?php
												}
												?>
												<div class="wrapper p-3 d-flex flex-column flex-fill">
													<div>
														<?php
														// echo($cat ? '<span class="border border-secondary text-secondary rounded small py-0 lh-1 px-2 me-2">' . $cat[0]->name . '</span>' : '');
														//if type is wp_term
														if (is_array($cat) && !empty($cat)) {
															echo($cat ? '<span class="border border-secondary text-secondary rounded small py-0 lh-1 px-2 me-2">' . $cat[0]->name . '</span>' : '');
														}
														if (is_array($type) && !empty($type)) {
															echo($type ? '<span class="border border-secondary text-secondary rounded small py-0 lh-1 px-2 me-2">' . $type[0]->name . '</span>' : '');
														}
														if (is_array($event_type) && !empty($event_type)) {
															echo($event_type ? '<span class="border border-secondary text-secondary rounded small py-0 lh-1 px-2 me-2">' . $event_type[0]->name . '</span>' : '');
														}
														?>
													</div>
													<h4 class="resource-title h5 small pb-4 mt-2">
														<?php echo get_the_title($post); ?>
													</h4>
													<div class="mt-auto"><a href="<?php echo get_permalink($post); ?>" class="btn btn-primary">Read more</a></div>
												</div>

											</article>
										</div>

									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

			</section>

			<?php
			if (is_active_sidebar('lvl-single-footer-posts') && is_singular('post')) {
				dynamic_sidebar('lvl-single-footer-posts');
			}
			?>

		<?php else :

			the_content();

		endif;
	endwhile;
endif;

get_footer();