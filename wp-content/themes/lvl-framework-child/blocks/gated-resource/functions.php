<?php

// populate acf field field_resource_form_key with gravity form forms
add_filter('acf/load_field/key=field_resource_form_key', function ($field) {
	$forms = GFAPI::get_forms(true, false, 'title', 'ASC');
//	var_dumped($forms);
	$choices = [];
	foreach ($forms as $form) {
		$choices[$form['id']] = '[ID:' . $form['id'] . '] ' . $form['title'];
	}
	$field['choices'] = $choices;

	return $field;
});


function gated_resource_generate_resource_token($resource_id)
{
    // Use WordPress's authentication secret key for better security
    $secret_key = defined('AUTH_KEY') ? AUTH_KEY : 'your_secret_key';

    $data = [
        'resource_id' => $resource_id,
        'timestamp'   => time(),
        // Add a random nonce for additional security
        'nonce'       => wp_generate_password(12, false)
    ];

    return base64_encode(json_encode($data)) . '|' . hash_hmac('sha256', json_encode($data), $secret_key);
}

function gated_resource_validate_resource_token($token)
{
    // Use WordPress's authentication secret key
    $secret_key = defined('AUTH_KEY') ? AUTH_KEY : 'your_secret_key';

    $parts = explode('|', $token);
    if (count($parts) !== 2) {
        return false;
    }

    [$data_encoded, $hash] = $parts;
    $data = json_decode(base64_decode($data_encoded), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return false;
    }

    // Verify hash
    if (!hash_equals($hash, hash_hmac('sha256', json_encode($data), $secret_key))) {
        return false;
    }

    // Check token expiration (24 hours)
    if (time() - $data['timestamp'] > 86400) {
        return false;
    }

    return $data['resource_id'];
}

add_action('admin_post_nopriv_download_resource', 'gated_resource_handle_resource_download');
add_action('admin_post_download_resource', 'gated_resource_handle_resource_download');

function gated_resource_handle_resource_download() {
    if (empty($_GET['token'])) {
        wp_die('Invalid request.');
    }

    $resource_id = gated_resource_validate_resource_token($_GET['token']);
    if (!$resource_id) {
        wp_die('Invalid or expired token.');
    }

    $file = get_attached_file($resource_id);
    if (!$file || !file_exists($file)) {
        wp_die('File not found.');
    }

    // Serve the file
    header('Content-Type: ' . mime_content_type($file));
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    readfile($file);
    exit;
}

add_action('admin_post_nopriv_get_resource_token', 'gated_resource_handle_token_request');
add_action('admin_post_get_resource_token', 'gated_resource_handle_token_request');

function gated_resource_handle_token_request() {
    // Set headers for JSON response
    header('Content-Type: application/json');

    if (empty($_GET['token'])) {
        echo json_encode(['success' => false, 'message' => 'Token is missing']);
        exit;
    }

    $token = sanitize_text_field($_GET['token']);
    $resource_id = gated_resource_validate_resource_token($token);

    if (!$resource_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
        exit;
    }

    // Generate the direct download URL
    $download_url = admin_url('admin-post.php?action=download_resource&token=' . urlencode($token));

    echo json_encode([
        'success' => true,
        'token_url' => $download_url,
        'resource_id' => $resource_id
    ]);
    exit;
}