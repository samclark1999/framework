<!DOCTYPE html>
<?php
$lang = esc_attr(get_field('lang', 'option') ?: 'en');
?>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="<?php echo $lang; ?>"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" lang="<?php echo $lang; ?>"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" lang="<?php echo $lang; ?>"> <![endif]-->
<!--[if gt IE 8]><!-->
<!--[if lt IE 10]>
<html class="no-js ie-9" lang="<?php echo $lang; ?>"><![endif]-->
<html class="no-js" lang="<?php echo $lang; ?>">
<!--<![endif]-->

<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="format-detection" content="telephone=no">
	<meta name="IE_RM_OFF" content="true">

	<title><?php wp_title(); ?></title>

	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">

	<?php wp_head(); ?>
</head>
<?php
//$trans_nav = false; // This wasn't set, so was filling up the php error log on the server
$behavior = 'default';//get_field('behavior', 'option');
$logo_light = get_field('site_logo_light', 'options');
$logo_dark = get_field('site_logo_dark', 'options');

$header_classes = [];
$header_attrs = [];
$navbar_attrs = [];

add_filter('body_class', function ($classes) {
	$classes[] = 'default-nav';

	return $classes;
});
?>
<body data-bs-theme="light" <?php body_class(); ?>>
<a tabindex="1" href="#skipNav" class="z-9999 visually-hidden-focusable px-3 py-2 btn btn-primary position-absolute">Skip Navigation</a>
<!--	<div class="site-wrapper">-->
<header id="header" data-bs-theme="dark" style="pointer-events: none;" class="<?php echo implode(' ', $header_classes); ?>" <?php echo implode(" ", $header_attrs) ?>>
	<div id="navbar" class="nav-wrapper">
		<div class="main-nav-wrapper navbar navbar-expand-lg p-lg-0 px-md-4 px-lg-5" aria-label="Main Navigation">
			<div class="nav-container container-lg-fluid d-block d-lg-flex justify-content-center py-md-2 border-top-0">
				<div class="brand-wrapper">
					<span class="navbar-brand">
						<?php if (!empty($logo_light)) {
							echo wp_get_attachment_image($logo_light, 'medium_large', false, ['class' => 'logo-light', 'loading' => 'eager']);
						}; ?>
						<?php if (!empty($logo_dark)) {
							echo wp_get_attachment_image($logo_dark, 'medium_large', false, ['class' => 'logo-dark', 'loading' => 'eager']);
						}; ?>

					</span>
				</div>
			</div>
		</div>
	</div>
</header>

<div id="skipNav" class="visually-hidden"></div>

<main id="main">