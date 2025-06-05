<?php global $post;
if (!defined('ABSPATH')) {
	exit;
}

$schema_type = match (get_post_type()) {
	'post' => 'BlogPosting',
	'event' => 'Event',
	'vendor' => 'Organization',
	'press-release' => 'NewsArticle',
	'video-podcast' => 'PodcastEpisode',
	'resource' => 'CreativeWork',
	default => 'WebPage',
};

$schema = [
		'@context'         => 'https://schema.org',
		'@type'            => $schema_type,
		'headline'         => get_the_title(),
		'datePublished'    => get_the_date('Y-m-d'),
		'dateModified'     => get_the_modified_date('Y-m-d'),
		'author'           => [
				'@type' => 'Organization',
				'name'  => get_bloginfo('name'),
		],
		'publisher'        => [
				'@type' => 'Organization',
				'name'  => get_bloginfo('name'),
				'logo'  => [
						'@type'  => 'ImageObject',
						'url'    => get_site_icon_url(),
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
			$cat = get_the_terms($post, 'resource_type');
			?>

			<section class="single-article" style="padding-top: var(--wp--preset--spacing--90);">

				<article <?php post_class('type---post-single'); ?>>

					<div class="container">
						<div class="row mb-3">
							<div class="col-12 col-lg-10 col-xl-8 mt-4 m-auto">
								<?php
								echo($cat ? '<span class="d-inline-block lh-1 py-1 px-2 bg-primary rounded text-light border border-primary">' . $cat[0]->name . '</span>' : '');
								?>
								<h1 class="post-title"><?php the_title(); ?></h1>
							</div>
						</div>
					</div>

					<div class="post-content container<?php echo(has_blocks() ? '' : ' no-blocks'); ?>">
						<div class="row">
							<div class="col-12 col-lg-10 col-xl-8 m-auto">
								<?php the_content(); ?>
							</div>
						</div>
					</div>
				</article>
			</section>
			<section class="share-post">
				<div class="container mb-5">
					<div class="row align-items-end">
						<div class="col-12 col-lg-10 col-xl-8 m-auto">
							<?php echo get_template_part('includes/share'); ?>
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
									if (!is_wp_error($categories)) {
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