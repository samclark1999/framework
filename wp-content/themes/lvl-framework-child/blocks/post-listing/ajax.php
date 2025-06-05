<?php

if (!defined('ABSPATH')) {
	exit;
}

// Get Items
add_action('wp_ajax_lvl_block_post_listing_get', 'lvl_block_post_listing_get');
add_action('wp_ajax_nopriv_lvl_block_post_listing_get', 'lvl_block_post_listing_get');
function lvl_block_post_listing_get($options = []): mixed

{
	$is_post = $_SERVER['REQUEST_METHOD'] === 'POST';

	if (!$options) {
		$options = $_POST ?? [];
	}
	$options = array_merge($options, $_POST ?? []);
	$options = array_map(function ($option) {
		if (is_string($option) && (str_starts_with($option, '{') || str_starts_with($option, '['))) {
			return json_decode(stripslashes($option), true);
		}

		return $option;
	}, $options);

	$defaults = [
			'current_id'          => get_queried_object_id(),
			'initial_load'        => false,
			'card_layout'         => 'column',
			'first_post_featured' => false,
			'display_options'     => [],
			'randomize'           => false,
			'link_text'           => 'Learn More',
			'featured_posts'      => [],
			'filters'             => [],
			'pre_filter'          => false,
			'format'              => 'echo',
			'page'                => 1,
			'posts_per_page'      => get_option('posts_per_page'),
			'post_types'          => 'any',
			'is_disable_click'    => false,
			'event_status'        => ['active', 'upcoming'],
			'author'              => null,
			'show_count'          => false,
	];

	$defaults_display_options = [
			'date'           => false,
			'author'         => false,
			'excerpt'        => false,
			'link'           => false,
			'featured_image' => false,
			'meta'           => false,
	];

	$options = array_merge($defaults, $options);
	$options['display_options'] = array_merge($defaults_display_options, $options['display_options']);

	$page = (int)($options['page']) ?: 1;
	$post_types = $options['post_types'];
	$post_types = explode(',', $post_types);
	$display_options = $options['display_options'];
//	$display_options = json_decode( stripslashes( $display_options ), true );
	$display_options = array_map(function ($option) {
		if ($option === 'true') {
			return true;
		} elseif ($option === 'false') {
			return false;
		} else {
			return $option;
		}
	}, $display_options);

	// Keywords
	$keyword = array_filter($options['filters'], function ($object) {
		return $object['type'] === 'keyword';
	});

	$keyword = array_map(function ($object) {
		return $object['value'];
	}, $keyword);

	// Taxonomies
	$possible_taxonomies = get_taxonomies([
			'public' => true,
	], 'objects');

	$possible_taxonomies = array_filter($possible_taxonomies, function ($object) {
		return !in_array($object->name, [
				'post_format',
				'nav_menu',
				'link_category',
				'post_tag',
				'wp_pattern_category',
				'wp_template_part_area',
				'wp_theme',
		]);
	});

	$filtered_taxes = array_filter($options['filters'], function ($object) use ($possible_taxonomies) {
		return in_array($object['type'] ?? '', array_keys($possible_taxonomies));
	});

	$args = [
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => $options['posts_per_page'],
			'offset'         => ($page - 1) * $options['posts_per_page'],
	];

	if ($options['randomize']) {
		$args['orderby'] = 'rand';
	}

	if ($options['author']) {
		$args['author_name'] = $options['author'];
	}

	if ($filtered_taxes) {

		if($options['pre_filter']) {
			$args['tax_query'] = [
					'relation' => 'OR',
			];
		} else {
			$args['tax_query'] = [
					'relation' => 'AND',
			];
		}

		foreach ($filtered_taxes as $filter_tax) {
			$args['tax_query'][] = [
					'taxonomy' => $filter_tax['type'],
					'terms'    => $filter_tax['value'],
					'field'    => 'slug',
			];
		}
	}

	if ($keyword) {
		$args['s'] = implode(',', $keyword);
	}

	if($options['featured_posts']) {
		$args['tax_query'] = [
				'relation' => 'OR',
		];
	}

	if ($options['featured_posts'] && !($options['author'] || $filtered_taxes || $keyword)) {
		$args['post__not_in'] = array_map(function ($post) {
			if (is_object($post)) {
				return $post->id;
			}

			return $post;
		}, $options['featured_posts']);

		if ($page === 1) {
			$args['posts_per_page'] = $options['posts_per_page'] - count($options['featured_posts']);
		} else {
			$args['offset'] = ($page - 1) * $options['posts_per_page'] - count($options['featured_posts']);
		}
	}

	if ($options['current_id']) {
		$args['post__not_in'][] = $options['current_id'];
	}

	foreach ($options['event_status'] as $status) {
		if ($status === 'active') {
			$args['meta_query'][] = [
					'relation' => 'AND',
					[
							'key'     => 'schedule_start',
							'value'   => date('Y-m-d H:i:s'),
							'compare' => '<=',
							'type'    => 'DATETIME',
					],
					[
							'key'     => 'schedule_end',
							'value'   => date('Y-m-d H:i:s'),
							'compare' => '>=',
							'type'    => 'DATETIME',
					],
			];
		} elseif ($status === 'upcoming') {
			$args['meta_query'][] = [
					'key'     => 'schedule_start',
					'value'   => date('Y-m-d H:i:s'),
					'compare' => '>',
					'type'    => 'DATETIME',
			];
		} elseif ($status === 'past') {
			$args['meta_query'][] = [
					'key'     => 'schedule_end',
					'value'   => date('Y-m-d H:i:s'),
					'compare' => '<',
					'type'    => 'DATETIME',
			];
		}
	}

	if ($args['meta_query'] ?? false) {
		$args['meta_query']['relation'] = 'OR';
	}


//	// add sort order for custom field start then publish date
//    $args['meta_query'] = [
//        'relation' => 'OR',
//    ];
//	$args['meta_key'] = 'schedule_start';
//	$args['orderby'] = 'meta_value';
//	$args['order'] = 'DESC';

	$response = '';
	$postListing = new WP_Query($args);

	$show_featured = ($options['featured_posts'] && $page === 1 && !($options['author'] || $filtered_taxes || $keyword));
	$total_count = $postListing->found_posts + count($options['featured_posts']);
	$total_pages = ceil(($total_count) / $options['posts_per_page']);

	if ($options['show_count'] && $total_count > 0 && $page === 1) {
		$response .= '<div class="results-count grid-col-span-all">' . $total_count . ' Result' . ($total_count > 1 ? 's' : '') . '</div>';
	}

	ob_start(); ?>
	<?php if ($postListing->have_posts()):
	if ($show_featured && $page === 1) {
		if ($args['posts_per_page'] <= 0) {
			$postListing->posts = [];
		}

		$options['featured_posts'] = array_reverse($options['featured_posts']);
		foreach ($options['featured_posts'] as $featured_post) {
			$post = get_post($featured_post);
			$postListing->posts = array_merge([$post], $postListing->posts);
		}
	}

	$is_first = true;

	//if resource_type in keys of  possible_taxonomies use that
	$is_resource_type = array_key_exists('resource_type', $possible_taxonomies);

	foreach ($postListing->posts as $post) :

		$terms = [];
		$has_resource_type = false;
		// if ($is_resource_type) {
		// 	$terms = get_the_terms($post, 'resource_type');
		// 	if ($terms) {
		// 		$has_resource_type = true;
		// 	}
		// }

		if (!$has_resource_type) {
			// get first possible tax from post
			foreach ($possible_taxonomies as $taxonomy) {
				$terms = get_the_terms($post, $taxonomy->name);
				if ($terms) {
					break;
				}
			}
		}

		$date_format = get_option('date_format');
		$date_format_event = get_option('date_format') . ' @ ' . get_option('time_format');


//			$terms = get_the_terms( $post, 'category' );
//			$type        = get_the_terms( $post, 'post_listing_type' );
//			$event_type  = get_the_terms( $post, 'event_type' );
//			$excerpt     = wp_strip_all_tags( $post->post_content, true );
//			$excerpt     = wp_trim_words( $excerpt, 25, '...' );
		$default_img = get_field('default_card_img', 'options');
		$classes = [];
		$notes = [];

		$event_schedule = get_field('schedule', $post);
		$schedule_start = $event_schedule['start'] ?? null;
		$schedule_end = $event_schedule['end'] ?? null;
		$start = null;
		$end = null;
		$is_disabled = false;

		if ('event' === get_post_type($post)) {
			if ($schedule_start && $schedule_end) {
				$start = new DateTime($schedule_start);
				$end = new DateTime($schedule_end);
				$now = new DateTime();
				if ($now > $start && $now < $end) {
					$classes[] = 'event-active';
					$notes[] = '<strong class="text-primary">HAPPENING NOW</strong>';
				}
			}

			if ($schedule_start) {
				$start = new DateTime($schedule_start);
				$now = new DateTime();
				if ($now < $start) {
					$classes[] = 'event-upcoming';
					$notes[] = '<span class="text-primary">Upcoming</span>';
				}
			}

			if ($schedule_end) {
				$end = new DateTime($schedule_end);
				$now = new DateTime();
				if ($now > $end) {
					$classes[] = 'event-over';
					$notes[] = 'Event Over';
					$is_disabled = $options['is_disable_click'] == 'true';
				}
			}
		}

		if ('tribe_events' === get_post_type($post)) {
			$start = new DateTime(tribe_get_start_date($post, true, 'Y-m-d H:i:s'));
		}

		$classes = implode(' ', $classes);

		$has_featured_image = has_post_thumbnail($post);

		$first_post_class = '';
		if ($options['first_post_featured'] && $is_first && $page === 1) {
			$first_post_class = 'grid-col-span-all'; // add more classes here if needed
			$is_first = false;
		}
		?>
		<div class="card-wrapper <?php echo(!$options['initial_load'] ? ' --loading' : '') ?> <?php echo $first_post_class; ?>" data-lvl-stretch-link="true">
			<article id="post-listing-<?php echo $post->ID; ?>"<?php echo($is_disabled ? ' disabled="disabled"' : '') ?> data-bs-theme="light" class="card-inner shadow gap-3 d-flex flex-<?php echo $options['card_layout']; ?> w-100<?php echo($classes ? ' ' . $classes : ''); ?>">
				<?php if (($options['card_layout'] === 'column' && $display_options['meta']) || ($display_options['featured_image']) && ($has_featured_image || $default_img)) {

					?>
					<div class="card-upper p-4 pb-0">
						<?php
						if (($display_options['featured_image']) && $has_featured_image) {
							?>
							<figure class="card-image-banner mb-0 rounded">
								<?php
								$image = wp_get_attachment_image(get_post_thumbnail_id($post), 'medium_large', false, ["class" => "object-fit--cover"]);

								// if EWWWIO image optimizer is active, check for webp
								global $eio_js_webp;
								if (!empty($eio_js_webp)) { // To make sure the class exists.
									$file_orig = get_attached_file(get_post_thumbnail_id($post));
									$file = $file_orig . '.webp';

									if (file_exists($file)) {
										$filename = basename($file_orig);
										$file_ext = pathinfo($filename, PATHINFO_EXTENSION);

										$image = str_replace($file_ext, $file_ext . '.webp', $image);
									}
								}

								echo $image;
								?>
							</figure>
							<?php
						} elseif (($display_options['featured_image']) && $default_img) {
							?>
							<figure class="card-image-banner mb-0">
								<?php echo wp_get_attachment_image($default_img, 'medium_large', false, ["class" => "object-fit--cover"]); ?>
							</figure>
							<?php
						}
						?>
					</div>
				<?php } ?>
				<div class="wrapper p-<?php echo($options['card_layout'] === 'column' ? '4' : '4 py-lg-5') ?> d-flex flex-column flex-fill">
					<?php if ($display_options['meta'] && $terms) { ?>
						<div class="text-primary mt-n4 text-uppercase">
							<?php
							echo $terms[0]->name;
							//							$filter_url = get_term_link($terms[0]);
							//							$post_type = get_post_type($post);
							//							$archive_page = get_option($post_type . '_archive_page');
							//
							//							if ($archive_page) {
							//								$page = get_post($archive_page);
							//								$filter_url = get_permalink($page) . '?category=' . $terms[0]->slug;
							//							} elseif ($post_type === 'post') {
							//								$filter_url = get_permalink(get_option('page_for_posts')) . '?category=' . $terms[0]->slug;
							//							}
							//
							//							echo '<a href="' . $filter_url . '">' . $terms[0]->name . '</a>';
							?>
						</div>
					<?php } ?>

					<h4 class="post-listing-title mb-3" id="<?php echo 'title-' . $post->ID; ?>">
						<?php echo get_the_title($post); ?>
					</h4>
					<div class="d-flex gap-1 align-items-center fs-5 fst-italic">
						<?php if ($display_options['date']) { ?>
							<div class="date mb-3"><?php echo get_the_date($date_format, $post); ?></div>
						<?php } ?>
						<?php if ($display_options['author']) { ?>
							<div class="author mb-3"><em><?php echo __('by ', 'theme') . get_the_author_meta('display_name', get_post_field('post_author', $post->ID));; ?></em></div>
						<?php } ?>
					</div>
					<?php
					if (($display_options['excerpt'])) {
						echo '<div class="post-listing-excerpt mt-3 mb-4">' . lvl_custom_excerpt($post) . '</div>';
					}

					foreach ($notes as $notes) {
						echo '<div class="mb-2">' . $notes . '</div>';
					}

					if ($start && $end) {
						// if day is the same, only show one date
						if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
							echo '<div class=" fs-5 fst-italic date mb-2"><em>' . $start->format($date_format_event) . '</em></div>';
						} else {
							echo '<div class=" fs-5 fst-italic date mb-2"><em>' . $start->format($date_format_event) . ' - ' . $end->format($date_format_event) . '</em></div>';
						}
					} elseif ($start) {
						echo '<div class=" fs-5 fst-italic date mb-2"><em>' . $start->format($date_format_event) . '</em></div>';
					} elseif ($end) {
						echo '<div class=" fs-5 fst-italic date mb-2"><em>' . $end->format($date_format_event) . '</em></div>';
					}

					?>
					<?php
					if ($is_disabled) {
						echo '<div class="small mt-auto mb-2 bg-danger-subtle px-2">' . __('This event has ended', 'theme') . '</div>';
					} else {
						?>
						<div class="mt-auto<?php echo(($display_options['link']) ? '' : ' visually-hidden'); ?>">
							<a href="<?php echo get_permalink($post); ?>" class="btn btn-link" aria-labelledby="#<?php echo 'title-' . $post->ID; ?>">
								<?php _e($options['link_text'], 'theme'); ?> <span class="visually-hidden"><?php echo __('about ', 'theme') . get_the_title($post); ?></span>
							</a>
						</div>
						<?php
					}
					?>
				</div>

			</article>
		</div>

	<?php endforeach; ?>

<?php else : ?>

	<div class="text-center py-5 grid-col-span-all">
		<p><?php _e('No items were found, please adjust filters and try again', 'theme'); ?></p>
		<button class="btn btn-link filter-reset">Clear Filters</button>
	</div>

<?php endif; ?>

	<?php

	$response .= ob_get_clean();


//	add_filter( 'eio_filter_admin_ajax_response', true );
//	add_filter( 'eio_allow_admin_js_webp', true );
//    //Otherwise, to do it with JS WebP, this requires a bit of development, and the use of these filters:
//	//eio_filter_admin_ajax_response
//	//eio_allow_admin_js_webp
//	//
//	//Additionally, you'd need to trigger the parsing function, potentially like this:
//	global $eio_js_webp;
//    var_dumped($eio_js_webp);
//	if ( ! empty( $eio_js_webp ) ) { // To make sure the class exists.
//		add_filter( 'the_content', array( $eio_js_webp, 'filter_page_output' ) );
//	}

	$headers = [
			'X-Total-Count' => $total_count,
			'X-Page'        => $page,
			'X-Per-Page'    => $options['posts_per_page'],
	];

	header('pagination:{"totalPages":' . $total_pages . ', "currentPage":' . $page . "}");
	header('loadmore:' . ((($total_count) > $page * $options['posts_per_page']) > 0 ? 'true' : 'false'));

	if ($options['format'] === 'html') {
		return [
				'html' => $response,
				'headers' => $headers,
		];
	}

//    $response = apply_filters('the_content', $response);
//    var_dumped($response);
	echo $response;

	die();
}