<?php if (!defined('ABSPATH')) {
	exit;
}

$render = function ($block, $is_preview, $content) {

	$the_block = new Level\Block($block);
	$show_filter = $the_block->getField('show_filter');
	$taxonomies = $the_block->getField('taxonomies') ?: [];
	$post_types = $the_block->getField('post_types') ?: [];
	$taxonomies_pre_filter = $the_block->getField('taxonomies_pre_filter') ?: [];
	$terms_pre_filter = $the_block->getField('terms_pre_filter') ?: [];
	$event_status = $the_block->getField('event_status') ?: [];
	$is_disable_click = $the_block->getField('is_disable_click') ?: false;
	$pagination = $the_block->getField('pagination') ?: 'none';
	$per_page = $the_block->getField('per_page') ?: get_option('posts_per_page');
	$display_options = $the_block->getField('display_options') ?: [];
	$link_text = $the_block->getField('link_text') ?: 'Learn More';
	$min_height = $the_block->getField('min_height') ? $the_block->getField('min_height') . 'px' : 'auto';
	$featured_posts = $the_block->getField('featured_posts') ?: [];
	$cards_per_row = $the_block->getField('cards_per_row') ?: 3;
	$layout = $the_block->getField('card_layout') ?: 'column';
	$first_featured = $the_block->getField('first_post_featured');
	$show_result_count = $the_block->getField('show_result_count') ?: false;
	$randomize = $the_block->getField('randomize') ?: false;
	$per_page_notice = '';
	$current_id = get_the_ID();

	$filters = [];

	if (is_archive())
		$current_id = get_queried_object_id();

	$the_block->addAttribute(['data-post-id' => $current_id]);
	$the_block->addAttribute(['data-init' => 'true']);
	$the_block->addAttribute(['data-pagination' => $pagination]);

	if ($is_preview) {
		$the_block->addAttribute(['data-lvl-preview' => 'true']);

		if ($per_page > 12) {
			$per_page_notice = $the_block->previewNotice('info', __('The Display Count is set to ' . $per_page . ', but only 12 are show in the editor for brevity.', 'theme'));
			$per_page = 12;
		}
	}

	if ($is_disable_click) {
		$the_block->addAttribute(['data-disable-click' => 'true']);
	}

	if ($event_status) {
		$the_block->addAttribute(['data-event-status' => json_encode($event_status)]);
	}

	$display_options = array_flip($display_options);
	$display_options = array_map(function ($value) {
		return true;
	}, $display_options);

	foreach ($display_options as $key => $option) {
		$the_block->addAttribute(['data-lvl-' . $key => 'true']);
		$the_block->addAttribute(['data-update' => 'updating']);
	}

	$the_block->addAttribute(['data-link-text' => $link_text]);
//    $the_block->addAttribute( [ 'data-post-id' => get_the_ID() ] );

	if ($min_height !== 'auto') {
		$the_block->addStyle('--min-height:' . $min_height);
	} else {
		$the_block->addAttribute(['data-lvl-layout' => 'square']);
	}

	$the_block->addStyle('--card-count:' . $cards_per_row);

	// build $terms_pre_filter as filters
	$pre_filter = [];
	if ($terms_pre_filter) {
		foreach ($terms_pre_filter as $key => $term_id) {
			$term = get_term($term_id);
			if ($term) {
				$pre_filter[] = [
						'type'  => $term->taxonomy,
						'value' => $term->slug,
				];
			}
		}
	}

	$filters = array_merge($filters, $pre_filter);

	// Filter for related posts
	$rel_filters = [];
	$taxonomies = get_post_taxonomies();
	foreach ($taxonomies as $tax) {
		$terms = get_the_terms(get_the_ID(), $tax);

		if ($terms) {

			foreach ($terms as $term) {
				$rel_filters[] = [
						'type'  => $term->taxonomy,
						'value' => $term->slug,
				];
			}
		}
	}

	$filters = array_merge($filters, $rel_filters);
	
	$the_block->addAttribute([
			'data-featured-posts' => json_encode($featured_posts),
			'data-init'           => 'true',
			'data-card-layout'    => $layout,
			'data-show-count'     => $show_result_count,
			'data-pre-filter'     => json_encode($pre_filter),
	]);

	ob_start();

	if ($is_preview) {
		$the_block->addClass('--preview');

//		$the_block->addAttribute( [
//			'data-taxonomies'      => json_encode( $taxonomies ),
//			'data-post-types'      => json_encode( $post_types ),
//			'data-pagination'      => $pagination,
//			'data-per-page'        => $per_page,
//			'data-display-options' => json_encode( $display_options ),
//			'data-min-height'      => $min_height,
//		] );

	}

	?>

	<div class="post-listing">


		<div class="row<?php echo($show_filter ? '' : ' d-none') ?>">
			<div class="col-12">
				<div class="p-3 p-md-4 px-lg-5">
					<div class="filter-bar row align-items-center" data-post-types="<?php echo esc_attr(implode(',', $post_types)); ?>">
						<?php

						if (is_author()) {
							$author = get_queried_object();
							?>
							<div class="filter col-12">
								<h2 class="h3"><?php _e('Posts by', 'theme'); ?><?php echo $author->display_name; ?></h2>
								<p class="mb-4"><a href="<?php echo get_permalink(get_option('page_for_posts')); ?>"><?php _e('Return to Full Listing', 'theme'); ?></a></p>
							</div>
							<?php
						}
						?>



						<?php
						if ($taxonomies) {
							foreach ($taxonomies as $filter) {
								$taxonomy = get_taxonomy($filter);
								if (!$taxonomy) {
									continue;
								}
								?>
								<div class="filter col-12 col-lg mt-4 mt-md-2">
									<div class="dropdown">
										<button class="dropdown-toggle btn border text-body-secondary rounded fw-normal" type="button" id="<?php echo $filter; ?>_select" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false"><?php echo __('Select a ', 'theme') . $taxonomy->labels->singular_name; ?></button>
										<ul id="type_filter" class="dropdown-menu" data-bs-offset="0,10" aria-labelledby="<?php echo $filter; ?>_select">
											<?php

											$terms = get_terms([
													'taxonomy'   => $filter,
													'hide_empty' => true,
													'exclude'    => 1,
											]);

											$paths = explode('/', $_SERVER['REQUEST_URI']);
											$is_taxonomy = false;

											$taxonomy_rewrite = explode('/', $taxonomy->rewrite['slug'] ?? '');

											foreach ($taxonomy_rewrite as $key => $value) {
												if (in_array($value, $paths)) {
													$is_taxonomy = true;
												} else {
													$is_taxonomy = false;
													break;
												}
											}

											foreach ($terms as $term) {
												$is_active = false;

												if ($is_taxonomy && in_array($term->slug, $paths)) {
													$is_active = true;

													$filters[] = [
															'type'  => $term->taxonomy,
															'value' => $term->slug,
													];
												}
												?>
												<li class="dropdown-item" data-is-active="<?php echo($is_active ? 'true' : 'false') ?>" data-filter-type="<?php echo $term->taxonomy; ?>" data-filter-value="<?php echo $term->slug; ?>" data-filter-value-id="<?php echo $term->term_id; ?>" tabindex="0"><?php echo $term->name; ?></li>
											<?php } ?>
										</ul>
									</div>
								</div>
								<?php
							}
						}
						?>

						<div class="filter col-12 col-lg mt-4 mt-md-2">
							<label for="keyword" class="visually-hidden">Keyword search</label>
							<input class="form-control mt-0" name="keyword" id="keyword" placeholder="Keyword search" data-filter-type="keyword" data-filter-value=""/>
						</div>

						<div class="filter col-12 col-lg-auto mt-4 mt-md-2">
							<button class="w-100 btn btn-primary filter-submit"><span class="">Filter</span></button>
						</div>

						<div class="filter col-12 col-md-auto mt-4 mt-md-2 d-none">
							<input type="hidden" name="perpage" id="perpage" value="<?php echo $per_page; ?>">
							<button class="btn btn-outline-primary filter-reset ms-3 d-none">Reset</button>
						</div>
					</div>
				</div>

				<div class="filtered py-3 text-center" aria-live="polite"></div>
			</div>
		</div>


		<div class="results">
			<div class="post-listing-target" aria-live="polite">
				<?php
				//if any query strings make taxonomies then add them to the filters array
				foreach ($_GET as $key => $value) {
					foreach ($taxonomies as $taxonomy) {
						if ($key === $taxonomy) {
							$filters[] = [
									'type'  => $taxonomy,
									'value' => $value,
							];
						}
					}
				}

				// PRE FILTER FROM BLOCK SETTINGS
				//				$is_prefilter = false;
				//				foreach ($terms_pre_filter as $key => $term_id) {
				//					$term = get_term($term_id);
				//					if ($term) {
				//						$filters[] = [
				//								'type'  => $term->taxonomy,
				//								'value' => $term->slug,
				//						];
				//
				//						$is_prefilter = true;
				//					}
				//				}

				$args = [
						'current_id'          => $current_id,
						'post_types'          => implode(',', $post_types),
						'posts_per_page'      => $per_page,
						'author'              => is_author(),
						'is_disable_click'    => $is_disable_click,
						'event_status'        => $event_status,
						'display_options'     => $display_options,
						'link_text'           => $link_text,
						'card_layout'         => $layout,
						'first_post_featured' => $first_featured,
						'featured_posts'      => $featured_posts,
						'filters'             => $filters,
						'pre_filter'          => $pre_filter,
						'show_count'          => $show_result_count,
						'randomize'           => $randomize,
						'page'                => 1,
						'initial_load'        => true,
						'format'              => 'html',
				];
		$response = lvl_block_post_listing_get($args);
		$headers = $response['headers'] ?? [];
		if ($response) {
		    echo $response['html'];
		}
				$total_pages = ceil(($headers['X-Total-Count'] ?? 1) / $args['posts_per_page']);
				$current_page = $args['page'] ?? 1;
		?>
		</div>
		<div class="spinner my-4 text-center" style="display: none;">
		    <div class="spinner-border" role="status">
		        <span class="visually-hidden">Loading...</span>
		    </div>
		</div>
		<?php
		if ($is_preview) {
		    echo $per_page_notice;
		}

		if ($pagination === 'load_more') {
		    ?>
		    <div>
		        <button class="btn btn-primary load-more m-auto mt-5 px-5<?php echo ($total_pages > 1 ? ' d-block' : ''); ?>">LOAD MORE</button>
		    </div>
		    <?php
		} elseif ($pagination === 'pages') {
		    ?>
		    <div>
		        <div class="pagination m-auto mt-5 justify-content-center d-flex">
		            <?php
		            if ($total_pages > 1) {
		                // Previous page arrow
		                $prev_page = max(1, $current_page - 1);
		                echo '<a class="page-link' . ($current_page == 1 ? ' disabled' : '') . '" href="#" data-page="' . $prev_page . '">
		                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="25" viewBox="0 0 15 25" fill="none"><path d="m12.5 2-10.5 10.5 10.5 10.5" fill="none" stroke="currentColor" stroke-width="3"></path></svg>
		                </a>';

		                // Always show first page
		                $active_class = ($current_page == 1) ? ' active' : '';
		                echo '<a class="page-link' . $active_class . '" href="#" data-page="1">1</a>';

		                // Show ellipsis if needed
		                if ($current_page > 4) {
		                    echo '<a class="page-link disabled" href="#" data-page="...">...</a>';
		                }

		                // Show pages around current page
		                $start_page = max(2, $current_page - 2);
		                $end_page = min($total_pages - 1, $current_page + 2);

		                for ($i = $start_page; $i <= $end_page; $i++) {
		                    if ($i >= 2 && $i < $total_pages) {
		                        $active_class = ($i == $current_page) ? ' active' : '';
		                        echo '<a class="page-link' . $active_class . '" href="#" data-page="' . $i . '">' . $i . '</a>';
		                    }
		                }

		                // Show ellipsis if needed
		                if ($current_page < $total_pages - 3) {
		                    echo '<a class="page-link disabled" href="#" data-page="...">...</a>';
		                }

		                // Always show last page if not the first page
		                if ($total_pages > 1) {
		                    $active_class = ($current_page == $total_pages) ? ' active' : '';
		                    echo '<a class="page-link' . $active_class . '" href="#" data-page="' . $total_pages . '">' . $total_pages . '</a>';
		                }

		                // Next page arrow
		                $next_page = min($total_pages, $current_page + 1);
		                echo '<a class="page-link' . ($current_page == $total_pages ? ' disabled' : '') . '" href="#" data-page="' . $next_page . '">
		                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="25" viewBox="0 0 15 25" fill="none"><path d="M2 23L12.5 12.5L2 2" stroke="currentColor" stroke-width="3"></path></svg>
		                </a>';
		            } elseif ($is_preview) {
		                echo $the_block->previewNotice('info', __('Pagination will be displayed here.', 'theme'));
		            }
		            ?>
		        </div>
		    </div>
		    <?php
		}
		?>
		</div>
	</div>


	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection($output);
};

$render($block ?? null, $is_preview ?? false, $content ?? '');
