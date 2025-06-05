<?php
get_header('landing-page');

if (have_posts()) {
	while (have_posts()) {
		the_post();

		the_content();

	}
}

get_footer('landing-page');