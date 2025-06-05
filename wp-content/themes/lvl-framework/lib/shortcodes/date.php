<?php if( ! defined('ABSPATH') ) exit;
//[callout][/callout]
add_shortcode('date', 'lvl_date_handler');
function lvl_date_handler( $atts, $content = null) {

    extract(shortcode_atts([
        'format' => 'F j, Y'
    ], $atts));

    $date = date_i18n($format);

		return $date;
}