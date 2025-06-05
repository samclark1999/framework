<?php if (!defined('ABSPATH')) exit;

// var_dump($block); die();

$render = function ($block, $is_preview, $content) {

	$the_block = new Level\Block($block);

	$id = $the_block->getId();

	$video_type = $the_block->getField('video_type');
	$video_url = $the_block->getField('video_url');
//    $video_embed = $the_block->getField('video_embed');
	$video_file = $the_block->getField('video_file');
	if ($video_type === 'file' && $video_file) {
		$video_url = $video_file['url'];
	}

	if (!$video_url && !$is_preview)
		return;

	$poster = $the_block->getField('video_poster');

	// if post false try to get poster/thumbnail image from video from internet
	if (!$poster && $video_url) {
		// if is youtube
		if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
			$video_id = '';

			// Extract video ID from URL
			if (strpos($video_url, 'youtube.com') !== false) {
				$parts = explode('v=', $video_url);
				if (isset($parts[1])) {
					$video_id = explode('&', $parts[1])[0]; // Remove any extra parameters
				}
			} elseif (strpos($video_url, 'youtu.be') !== false) {
				$parts = explode('/', $video_url);
				if (isset($parts[3])) {
					$video_id = explode('?', $parts[3])[0]; // Remove any extra parameters
				}
			}

			if ($video_id) {
				// Check transient first
				$transient_key = 'youtube_poster_' . $video_id;
				$poster = get_transient($transient_key);

				if ($poster === false) {
					// Transient doesn't exist or has expired, check if maxresdefault exists
					$maxres_url = 'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg';
					$response = wp_remote_head($maxres_url);

					if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
						// Maxres not available, use hqdefault
						$poster = 'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg';
					} else {
						// Maxres is available
						$poster = $maxres_url;
					}

					// Store in transient for 1 week
					set_transient($transient_key, $poster, 7 * DAY_IN_SECONDS);
				}
			}
		}

		// if is vimeo
		if (strpos($video_url, 'vimeo.com') !== false) {
			$poster = 'https://vumbnail.com/' . explode('/', $video_url)[3] . '.jpg';
		}
	}


	ob_start();

	if ($is_preview && !$video_url) {
		echo $the_block->previewNotice('info', 'Please select a video file or URL to display the video.');
	}
	?>
	<div class="video-player--wrapper">
		<div class="backdrop--"></div>
		<div id="video-player-<?php echo $the_block->getId() ?>" class="video-player"></div>
		<a href="<?php echo $video_url; ?>" class="video-player__link visually-hidden" tabindex="-1" target="_blank">Watch video</a>
	</div>
	<div class="video-wrapper">
		<div tabindex="0" class="video"
				 data-src="<?php echo $video_url; ?>"
				 data-type="<?php echo $video_type; ?>"
				 style="background-image:url(<?php echo($poster ?: get_template_directory_uri() . '/dist/img/video-poster.webp'); ?>); "></div>
	</div>

	<?php

	$output = ob_get_clean();

	echo $the_block->renderSection($output, 'basic');

};

$render($block, $is_preview, $content);