<?php
// register new wp settings page and options for Gravity Form Admin Notifications - From Email, From Name, Reply To Email
add_action('admin_init', 'lvl_child_register_GF_settings');
function lvl_child_register_GF_settings()
{
	// Register the settings
	register_setting('gf_notifications', 'gf_notifications_from_email');
	register_setting('gf_notifications', 'gf_notifications_from_name');
	register_setting('gf_notifications', 'gf_notifications_reply_to_email');

	// Add the settings section
	add_settings_section(
			'gf_notifications_section',
			__('Gravity Form Notifications Defaults', 'lvl-framework'),
			function () {
				echo '<p>' . __('Set default values for new Gravity Form notifications.', 'lvl-framework') . '</p>';
			},			'gf_notifications'
	);

	// Add the settings fields
	add_settings_field(
			'gf_notifications_from_email',
			__('From Email', 'lvl-framework'),
			function () {
				echo '<input type="email" name="gf_notifications_from_email" value="' . esc_attr(get_option('gf_notifications_from_email')) . '" />';
			},
			'gf_notifications',
			'gf_notifications_section'
	);

	add_settings_field(
			'gf_notifications_from_name',
			__('From Name', 'lvl-framework'),
			function () {
				echo '<input type="text" name="gf_notifications_from_name" value="' . esc_attr(get_option('gf_notifications_from_name')) . '" />';
			},
			'gf_notifications',
			'gf_notifications_section'
	);

	add_settings_field(
			'gf_notifications_reply_to_email',
			__('Reply To Email', 'lvl-framework'),
			function () {
				echo '<input type="email" name="gf_notifications_reply_to_email" value="' . esc_attr(get_option('gf_notifications_reply_to_email')) . '" />';
			},
			'gf_notifications',
			'gf_notifications_section'
	);
}

// Add the settings page to the admin menu
add_action('admin_menu', 'lvl_child_add_GF_settings_page');
function lvl_child_add_GF_settings_page()
{
	add_options_page(
			__('Gravity Form Notifications', 'lvl-framework'),
			__('Gravity Form Notifications', 'lvl-framework'),
			'manage_options',
			'gf_notifications',
			'lvl_child_GF_settings_page'
	);
}

// Render the settings page
function lvl_child_GF_settings_page()
{
	?>
	<div class="wrap">
		<h1><?php _e('Gravity Form Notifications', 'lvl-framework'); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields('gf_notifications');
			do_settings_sections('gf_notifications');
			submit_button();
			?>
		</form>
	</div>
	<?php
}

// filter to set default gravity form notifications
add_filter('gform_notification_settings_fields', function ($fields, $notification, $form) {
	foreach ($fields[0]['fields'] as &$field) {
// Set default values for notification fields if corresponding options exist
		$defaults = [
				'name' => ['option' => 'gf_notifications_from_name', 'value' => 'Notification'],
				'from' => ['option' => 'gf_notifications_from_email', 'value' => get_option('gf_notifications_from_email')],
				'fromName' => ['option' => 'gf_notifications_from_name', 'value' => get_option('gf_notifications_from_name')],
				'replyTo' => ['option' => 'gf_notifications_reply_to_email', 'value' => get_option('gf_notifications_reply_to_email')],
				'subject' => ['option' => 'gf_notifications_from_name', 'value' => 'New Form Submission: {form_title}'],
				'message' => ['option' => 'gf_notifications_from_name', 'value' => '{all_fields}']
		];

// Special case for fields that can have multiple names
		if (rgar($field, 'name') === 'toEmail' || rgar($field, 'name') === 'to') {
			if (get_option('gf_notifications_from_email')) {
				$field['default_value'] = get_option('gf_notifications_from_email');
			}
		} else {
			// Handle standard fields
			$fieldName = rgar($field, 'name');
			if (isset($defaults[$fieldName]) && get_option($defaults[$fieldName]['option'])) {
				$field['default_value'] = $defaults[$fieldName]['value'];
			}
		}
	}

	return $fields;
}, 10, 3);

//update gravity for block form selector to include form IDs
add_filter('gform_post_render', function ($form) {
	$form['id'] = 'gf_' . $form['id'];
	return $form;
}, 10, 1);