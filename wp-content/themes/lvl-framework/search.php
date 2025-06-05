<?php if (!defined('ABSPATH')) exit;

global $wp_query;
$search_query = get_search_query();
$total = $wp_query->found_posts ?: 0;
$paged = get_query_var('paged') ?: 1;

get_header(); ?>
	<section class="banner pb-3" style="padding-top: var(--wp--preset--spacing--90);">
		<div class="container">
			<h1><?php _e('Search', 'theme'); ?></h1>
		</div>
	</section>
	<section class="search-inline py-3 py-md-4">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 col-xl-6">
					<?php get_search_form(); ?>
				</div>
			</div>
		</div>
	</section>
<?php if (have_posts()) : ?>
	<section class="search-results">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 col-xl-8">
					<p class="mb-4 text-body-emphasis border-bottom"><?php echo $total . (($total === 1) ? __(' Result', 'theme') : __(' Results', 'theme')); ?></p>
					<ul class="search-results-list list-unstyled">
						<?php while (have_posts()) :
							the_post(); ?>
							<li <?php post_class('search-result mb-3 pb-3 border-bottom'); ?>>
								<small class="text-muted d-block mb-2"><?php echo str_replace('_', ' ', strtoupper(get_post_type())); ?></small>
								<h3 class="my-2"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p class="mb-3"><?php echo get_the_excerpt(); ?></p>
								<a href="<?php the_permalink(); ?>" class="d-block mw-100 text-truncate"><?php the_permalink(); ?></a>
							</li>
						<?php endwhile; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>
	<section class="container mb-5 pb-5">
		<nav class="pagination serp-pagination mt-4 mb-5 pb-5">
			<?php the_posts_pagination([
					'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="25" viewBox="0 0 15 25" fill="none"><path d="m12.5 2-10.5 10.5 10.5 10.5" fill="none" stroke="currentColor" stroke-width="3"></path></svg>',
					'next_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="25" viewBox="0 0 15 25" fill="none"><path d="M2 23L12.5 12.5L2 2" stroke="currentColor" stroke-width="3"></path></svg>',
			]); ?>
		</nav>
	</section>
<?php else: ?>
	<section class="search-results">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 col-xl-8">
					<p class="mb-4 text-body-emphasis border-bottom"><?php echo $total . (($total === 1) ? __(' Result', 'theme') : __(' Results', 'theme')); ?></p>
					<p><?php _e('Sorry, no results were found for your search.', 'theme'); ?></p>
					<p><?php _e('Please try again with different keywords.', 'theme'); ?></p>
				</div>
			</div>
		</div>
	</section>
<?php
endif;
get_footer();