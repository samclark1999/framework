<?php if (!defined('ABSPATH')) exit;

// Get Resources
add_action('wp_ajax_lvl_team_get', 'lvl_team_get');
add_action('wp_ajax_nopriv_lvl_team_get', 'lvl_team_get');
function lvl_team_get()
{

    $page = ($_POST['page']) ?: 1;

    $filters = $_POST['filters'];
    $filters = json_decode(stripslashes($filters));

    // Team Types
    $team_type = array_filter($filters, function ($object) {
        return $object->type === 'team-type';
    });

    $team_type = array_map(function ($object) {
        return $object->value;
    }, $team_type);

    // Keywords
    $keyword = array_filter($filters, function ($object) {
        return $object->type === 'keyword';
    });

    $keyword = array_map(function ($object) {
        return $object->value;
    }, $keyword);

    // Filters or Types
    $types = ($team_type) ?: explode(',', $_POST['types']);

    if (!$keyword) {

        foreach ($types as $type) {

            $args = [
                'post_type'      => 'team-member',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'date',
                'order'          => 'ASC',
            ];

            if($type) {
                $args['tax_query'] = [
                    [
                        'taxonomy' => 'team-type',
                        'field'    => 'term_id',
                        'terms'    => $type,
                    ],
                ];
            }

            if ($keyword) {
                $args['s'] = implode(',', $keyword);
            }

            $team = new WP_Query($args);

            ob_start();

            if ($team->have_posts()) : ?>

                <div class="team-members row">
                    <h2 class="text-center col-12"><?php echo get_term($type)->name; ?> Team</h2>

                    <?php foreach ($team->posts as $post) : ?>

                        <div class="member col-12 col-sm-6 col-lg-4 col-xl-3 mb-4 px-sm-2">
                            <div class="member-inner">
                                <div class="img">
                                    <?php
                                    echo wp_get_attachment_image(get_post_thumbnail_id($post->ID), 'medium_large', false, ['class' => 'img-fluid']);
                                    //echo get_the_post_thumbnail($post->ID, 'medium_large', ['class' => 'img-fluid']);
                                    ?>
                                </div>
                                <div class="info">
                                    <div>
                                        <h4 class="mb-2"><?php echo get_the_title($post->ID); ?></h4>
                                        <p><?php echo get_field('role', $post->ID); ?></p>
                                    </div>
                                    <?php if (get_field('bio', $post->ID) && get_field('bio', $post->ID) != '') : ?>
                                        <button type="button" class="btn modal-button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#teamModal"
                                                data-bs-name="<?php echo esc_attr(get_the_title($post->ID)); ?>"
                                                data-bs-role="<?php echo esc_attr(get_field('role', $post->ID)); ?>"
                                                data-bs-bio="<?php echo esc_attr(get_field('bio', $post->ID)); ?>"
                                                data-bs-image="<?php echo esc_attr(get_the_post_thumbnail_url($post->ID, 'medium_large')); ?>">
                                            <svg class="circle">
                                                <use xlink:href="#chevron-right" role="none" aria-hidden="true" focusable="false"></use>
                                            </svg>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else : ?>

                <div class="text-center py-5">
                    <p>no team members were found, please adjust filters and try again</p>
                    <button class="btn btn-link filter-reset">Clear Filters</button>
                </div>

            <?php endif;

        }

    } else {

        $args = [
            'post_type'      => 'team-member',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'ASC',
            'tax_query'      => [
                [
                    'taxonomy' => 'team-type',
                    'field'    => 'term_id',
                    'terms'    => $types,
                ],
            ],
        ];

        if ($keyword) {
            $args['s'] = implode(',', $keyword);
        }

        $team = new WP_Query($args);

        ob_start();

        if ($team->have_posts()) : ?>

            <div class="team-members row">

                <?php foreach ($team->posts as $post) : ?>

                    <div class="member col-12 col-sm-6 col-lg-4 col-xl-3 mb-4 px-sm-2">
                        <div class="member-inner">
                            <div class="img">
                                <?php echo get_the_post_thumbnail($post->ID, 'full', ['class' => 'img-fluid']); ?>
                            </div>
                            <div class="info">
                                <div>
                                    <h4 class="mb-2"><?php echo get_the_title($post->ID); ?></h4>
                                    <p><?php echo get_field('role', $post->ID); ?></p>
                                </div>
                                <?php if (get_field('bio', $post->ID) && get_field('bio', $post->ID) != '') : ?>
                                    <button type="button" class="btn modal-button" data-bs-toggle="modal" data-bs-target="#teamModal" data-bs-name="<?php echo get_the_title($post->ID); ?>" data-bs-role="<?php echo get_field('role', $post->ID); ?>" data-bs-bio="<?php echo get_field('bio', $post->ID); ?>" data-bs-image="<?php echo get_the_post_thumbnail_url($post->ID); ?>">
                                        <svg class="circle">
                                            <use xlink:href="#plus" role="none" aria-hidden="true" focusable="false"></use>
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

        <?php else : ?>

            <div class="text-center py-5">
                <p>no team members were found, please adjust filters and try again</p>
                <button class="btn btn-link filter-reset">Clear Filters</button>
            </div>

        <?php endif;

    }

    $response = ob_get_clean();

    echo $response;

    // header('loadmore:'. ($resources->found_posts > $page * 12));

    die();

}