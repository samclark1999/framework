<?php

namespace Level;

class Helper
{
	/**
	 * Get the excerpt by word count
	 *
	 * @param string $content
	 * @param int    $word_count
	 * @return string
	 */
	public static function excerpt_by_word_count($content, $word_count = 55): string
	{
		$content = strip_tags($content);
		$content = explode(' ', $content);
		$content = array_slice($content, 0, $word_count);
		$content = implode(' ', $content);
		return $content;
	}

	/**
	 * Get the age since published of a post
	 *
	 * @param $post int | \WP_Post
	 * @return string
	 */
	public static function days_since_published(int|\WP_Post $post): string
	{
		if (is_numeric($post)) {
			$post = get_post($post);
		}

		$days_since_published = intval(floor((time() - strtotime($post->post_date)) / (60 * 60 * 24)));
		$age = $days_since_published . ' days ago';
		if ($days_since_published === 0) {
			$age = 'today';
		} elseif ($days_since_published === 1) {
			$age = 'yesterday';
		} elseif ($days_since_published >= 365) {
			$days_since_published = intval(floor($days_since_published / 365));
			if ($days_since_published === 1) {
				$age = '1 year ago';
			} else {
				$age = $days_since_published . ' years ago';
			}
		} elseif ($days_since_published >= 30) {
			$days_since_published = intval(floor($days_since_published / 30));
			if ($days_since_published === 1) {
				$age = '1 month ago';
			} else {
				$age = $days_since_published . ' months ago';
			}
		} elseif ($days_since_published >= 7) {
			$days_since_published = intval(floor($days_since_published / 7));
			if ($days_since_published === 1) {
				$age = '1 week ago';
			} else {
				$age = $days_since_published . ' weeks ago';
			}
		}
		return $age;
	}

	public static function get_the_posts_pagination($transient = null): array|string
	{
		if ($transient && ($pagination = get_transient($transient))) {
			return $pagination;
		}

		$pagination = get_the_posts_pagination(
				[
						'class'     => 'mx-auto',
						'prev_text' => '<span class="btn btn-info"><span class="visually-hidden">Previous</span><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="15" height="25" viewBox="0 0 15 25" fill="none"><path d="m12.5 2-10.5 10.5 10.5 10.5" fill="none" stroke="currentColor" stroke-width="3"/></svg></span>',
						'next_text' => '<span class="btn btn-info"><span class="visually-hidden">Next</span><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="15" height="25" viewBox="0 0 15 25" fill="none"><path d="M2 23L12.5 12.5L2 2" stroke="currentColor" stroke-width="3"/></svg></span>',
						'mid_size'  => 4,
						'end_size'  => 1,
				]
		);

		// format to Bootstrap pagination
		$pagination = str_replace('nav-links', 'pagination align-items-center', $pagination);
		$pagination = str_replace('class="page-numbers"', 'class="page-link"', $pagination);
		$pagination = str_replace('class="page-numbers current"', 'class="page-link active"', $pagination);
		$pagination = str_replace('class="page-numbers dots"', 'class="page-link disabled"', $pagination);

		if ($transient) {
			set_transient($transient, $pagination, HOUR_IN_SECONDS);
		}

		return $pagination;
	}


	/**
	 * Convert hex color to rgb
	 *
	 * @param string $hex
	 * @return string
	 */
	public static function hex2rgb($hex): string
	{
		list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
		return "$r, $g, $b";
	}
}