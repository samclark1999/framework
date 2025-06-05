<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {
    $the_block = new LVLBlock($block);

    // Get the latest blog posts
    $blog_args = array(
        'post_type' => 'post',
        'posts_per_page' => 2,
        'orderby' => 'date',
        'order' => 'DESC',
        'post_status' => 'publish',
        'category__not_in' => array(get_category_by_slug('product-update')->term_id),
    );
    $blog_query = new WP_Query($blog_args);

    // Get the latest product update posts
    $product_update_args = array(
        'post_type' => 'post',
        'posts_per_page' => 2,
        'orderby' => 'date',
        'order' => 'DESC',
        'post_status' => 'publish',
        'category_name' => 'product-update',
    );
    $product_update_query = new WP_Query($product_update_args);
    
    ob_start(); ?>

    <div class="block--double-slide" <?php echo $the_block->renderAttribute(); ?>>
        <div class="row p-1 m-0 justify-content-evenly">
            <div class="blog-slide col-12 col-md-6 p-4">
                <div class="swiper swiper-blog">
                    <div class="swiper-wrapper">
                        <?php while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
                            <div class="swiper-slide">
                                <div class="category">Blog</div>
                                <h6><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                                <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <div class="product-update-slide col-12 col-md-6 p-4">
                <div class="swiper swiper-product-update">
                    <div class="swiper-wrapper">
                        <?php while ($product_update_query->have_posts()) : $product_update_query->the_post(); ?>
                            <div class="swiper-slide">
                                <div class="category">Product Update</div>
                                <h6><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                                <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    $output = ob_get_clean();
    echo $output;

    wp_reset_postdata();
};

$render($block, $is_preview, $content);
