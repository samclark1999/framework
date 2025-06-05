<?php if( ! defined('ABSPATH') ) exit;
// [icon icon="chevron-down" url="" size="md" xclass=""]
/**
 * @param $atts
 * @param $content
 * @return string
 *
 * @example [icon icon="chevron-down" url="" size="md" xclass=""]
 * @note This shortcode is used to display an icon from the SVG sprite sheet. Sizes are xs, sm, md, lg, and xl.
 */
function lvl_icon_shortcode($atts, $content = null ): string
{

    extract( shortcode_atts( [
        'icon' 		=> 'chevron-down',
        'url' 		=> '',
        'size' 		=> 'md',
        'xclass' 	=> ''
    ], $atts ) );

    $svg = '<svg preserveAspectRatio="xMidYMid slice" class="icon icon-' . $icon . ' icon-' . $size . ( $xclass ? ' ' . $xclass : '' ) . '" aria-hidden="true"><use xlink:href="#' . $icon . '"></use></svg>';


    if($url) {
        return '<a href="' . $url . '" class="icon-link">' . $svg . '</a>';
    } else {
        return $svg;
    }

}
add_shortcode( 'icon', 'lvl_icon_shortcode' );