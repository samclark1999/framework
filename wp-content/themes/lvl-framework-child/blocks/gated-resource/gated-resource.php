<?php if (!defined('ABSPATH')) {
	exit;
}

$render = function ($block, $is_preview, $content) {
	/**
	 * Block Template: Gated Resource
	 *
	 * @param array $block The block settings and attributes.
	 * @param bool  $is_preview True during backend preview render.
	 * @param int   $post_id The post ID the block is rendering content against.
	 */
	$the_block = new Level\Block($block ?? []);
	$resource_file = get_field('resource_file');
	$form_id = get_field('resource_form');
	$confirmation = get_field('confirmation_message');
	$page_id = get_the_ID();

	$the_block->addAttribute(['data-page-id' => $page_id]);

	ob_start(); ?>
	<div class="gated-resource-wrapper">
		<div class="gated-resource-content">
			<div class="resource-form">
				<?php
				if (!empty($form_id) && $form_id != '0' && function_exists('gravity_form')) {
					// Display the form with AJAX enabled and a custom callback
					gravity_form((int)$form_id, false, false, false, null, true, 1, true);
				} else {
					echo '<p>Form missing.</p>';
				}
				?>
			</div>
			<div class="resource-download d-none my-4">
				<div>
					<?php
					$resource_id = $resource_file['ID'] ?? 0;
					$resource_token = gated_resource_generate_resource_token($resource_id);
					?>
					<div class="d-flex flex-column align-items-center gap-4 p-3 pb-4 border rounded shadow bg-white" data-bs-theme="light">
						<?php echo esc_html($confirmation); ?>
						<a href="#"
							 class="btn btn-primary resource-download-btn"
							 data-resource="<?php echo esc_attr($resource_token); ?>">
							Download <?php echo esc_html($resource_file['title'] ?? ''); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	$output = ob_get_clean();

	echo $the_block->renderSection($output, 'basic');
};

$render($block, $is_preview, $content);