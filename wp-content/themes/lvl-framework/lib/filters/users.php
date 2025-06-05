<?php

namespace LVL\Framework\Filters;

const LVL_AGENCY_EMAIL_DOMAIN = ['@level.agency', '@webmechanix.com'];
const LVL_AGENCY_USERNAMES = ['wmxadmin', 'lvladmin', 'artemis'];

function is_user_from_agency($user_id)
{
		// Check if the user ID is valid
		if (!$user_id) {
				return false;
		}

		// Get the user's email
		$user_email = get_the_author_meta('user_email', $user_id);

		// Check if the email belongs to an agency domain
		foreach (LVL_AGENCY_EMAIL_DOMAIN as $domain) {
				if (str_contains($user_email, $domain)) {
						return true;
				}
		}

		// Check if the user ID is in the predefined usernames
		if (in_array(get_the_author_meta('user_login', $user_id), LVL_AGENCY_USERNAMES)) {
				return true;
		}

		return false;
}


if (!is_admin()) {
 /**
  * Filter agency admin user display name to be site title
  */
 add_filter('the_author', __NAMESPACE__ . '\\author_name');
 function author_name($name)
 {
  if (in_array($name, LVL_AGENCY_USERNAMES)) {
   return get_bloginfo('name');
  }

  global $authordata;

  if (!is_object($authordata)) {
   return $name;
  }

  if (is_user_from_agency($authordata->ID)) {
    return get_bloginfo('name');
  }

  return $name;
 }

 add_filter('the_author_posts_link', __NAMESPACE__ . '\\author_posts_link');

 function author_posts_link($link)
 {
  global $authordata;

  if (is_object($authordata) && is_user_from_agency($authordata->ID)) {
    return '';
  }

  return $link;
 }

 add_filter('get_the_author_meta', __NAMESPACE__ . '\\author_meta', 10, 3);

 function author_meta($value, $field, $author_id)
 {
  // Check if the author ID is from an agency
  if (is_user_from_agency($author_id) || in_array(get_the_author_meta('user_email', $author_id), LVL_AGENCY_USERNAMES)) {
   // Return site title for specific author meta requests
   if ($field === 'display_name' || $field === 'user_nicename') {
    return get_bloginfo('name');
   }
  }

  return $value;
 }

 /**
  * Filter agency admin user display name to be site title
  */
 add_filter('get_the_author_display_name', __NAMESPACE__ . '\\author_display_name', 10, 2);
 function author_display_name($display_name, $author_id)
 {
  if (is_user_from_agency($author_id)) {
    return get_bloginfo('name');
  }

  return $display_name;
 }

 /**
  * Filter agency admin user link to be empty
  */
 add_filter('author_link', __NAMESPACE__ . '\\author_link', 10, 3);
 function author_link($link, $author_id, $author_nicename)
 {
  if (is_user_from_agency($author_id)) {
    return '';
  }

  return $link;
 }
}

