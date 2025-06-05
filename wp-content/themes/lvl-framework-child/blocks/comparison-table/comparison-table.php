<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {

	$the_block = new Level\Block($block);

	$rows = $the_block->getField('rows', []) ?: [];


	ob_start();

	if ($is_preview && count($rows) < 1) {
		$the_block->addClass('--preview');
		echo $the_block->previewNotice('info', 'Please add rows to the comparison table block.');
	}

	$features = [];
	$plans = [];

	foreach ($rows as $row_key => $row) {
		$columns = $row['columns'] ?? [];

		foreach ($columns as $col_key => $column) {
			if ($row_key === 0) {
				$plans[$col_key][] = $column;
			} elseif ($col_key === 0) {
				$features[] = $column;
			} else {
				$plans[$col_key][$row_key] = $column;
			}
		}
	}

	$the_block->addAttribute([
			'data-plans' => (count($plans) - 1),
	]);

	ob_start(); ?>

	<div class="comparison-table table-responsive">
		<table class="table">
			<thead>
			<tr>
				<th class="feature"></th>
				<?php
				$count = 0;
				foreach ($plans as $plan) :
					$count++;
					if ($count == 1) continue;
					?>
					<th class="plan-header">
						<span><?php echo $plan[0]['label']; ?></span>
					</th>
				<?php endforeach; ?>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($features as $key => $feature) : ?>
				<tr>
					<td id="feature-<?php echo _wp_to_kebab_case($feature['label']) ?>" class="feature">
						<?php echo $feature['label']; ?>
					</td>
					<?php
					$count = 0;
					foreach ($plans as $plan) :
						$count++;
						if ($count == 1) continue;
						?>
						<td class="plan-feature <?php echo _wp_to_kebab_case($feature['label']) ?>" data-related-feature="feature-<?php echo _wp_to_kebab_case($feature['label']) ?>">
							<?php
							if ($plan[$key + 1]['disable_icon'] ?? false) {
								echo '<span class="">' . ($plan[$key + 1]['label'] ?? '') . '</span>';
							} else {
								if ($plan[$key + 1]['feature'] ?? false) {
									echo '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="34" height="34" viewBox="0 0 34 34" fill="none"> <g clip-path="url(#clip0_449_2518)"> <path d="M1.69995 17.5194L11.2747 27.2008L31.45 6.80078" stroke="#69B779" stroke-width="5"/> </g> <defs> <clipPath id="clip0_449_2518"> <rect width="34" height="34" fill="white"/> </clipPath> </defs> </svg>';
								} else {
									echo '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="34" height="34" viewBox="0 0 34 34" fill="none"> <path d="M5 29L29 5" stroke="#0E1832" stroke-width="5"/> <path d="M5 5L29 29" stroke="#0E1832" stroke-width="5"/> </svg>';
								}
								echo '<span class="visually-hidden">' . ($plan[$key + 1]['label'] ?? '') . '</span>';
							}
							?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection($output);
};

$render($block, $is_preview, $content);
