<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {

    $the_block = new Level\Block($block);
    if ($is_preview) {
        $the_block->addClass('--preview');
    }

    $label = $the_block->getField('tab_navigation_label') ?: '... add tab navigation label in sidebar';
    $id = ($block['anchor'] ?? false) ?: _wp_to_kebab_case($label);

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
                'content' => 'Esse error quam dolore nesciunt ut nemo quae illo fuga voluptatibus tenetur et dignissimos soluta.',
            ],
        ],
    ];

    $inner = '<InnerBlocks 
                template="' . esc_attr(wp_json_encode($innerBlocks)) . '" 
                templateLock="false" />';

    ob_start(); ?>

    <div class="block--tab-pane tab-pane<?php echo (!$is_preview) ? ' fade' : ' --preview'; ?>" id="tab-pane-<?php echo $id; ?>" data-tab-title="<?php echo esc_attr($label); ?>" role="tabpanel" aria-labelledby="tab-<?php echo _wp_to_kebab_case($label); ?>" tabindex="0">
        <?php
        if ($is_preview) {
//            echo '<div class="border p-2 mb-4 bg-primary-subtle">TAB PANE <button class="float-end btn-toggle-collapse--pane p-1 btn small btn-outline-dark"><span class="visually-hidden">Toggle pane</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/> </svg></button></div>';
//            echo '<div class="btn btn-primary mb-4">' . $label . '</div>';
//					echo $the_block->previewNotice('info', 'Tab Pane Block');
					echo '<div class="btn btn-outline-primary my-4">' . $label . '</div>';
        }
        ?>
        <div class="row align-items-center">
            <?php echo $inner; ?>
        </div>
    </div>

    <?php

    $output = ob_get_clean();

    echo $the_block->renderSection($output, false);

};

$render($block, $is_preview, $content);