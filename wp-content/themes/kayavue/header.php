<?php
/**
 * Site header.
 *
 * @package KayaVue
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content">Skip to content</a>
<div class="utility">
	<div class="wrap utility-inner">
		<p>Aesthetic care across Indian cities, explained without a sales script</p>
		<p>Not a clinic · Not medical advice</p>
	</div>
</div>
<header class="site-header">
	<div class="wrap header-inner">
		<a class="wordmark" href="<?php echo esc_url(home_url('/')); ?>">
			<svg class="mark" viewBox="0 0 32 32" width="32" height="32" aria-hidden="true">
				<rect width="32" height="32" rx="8" fill="#16352F"/>
				<path d="M9 23V12.2C9 8.8 12 7.4 16 7.4s7 1.4 7 4.8V23" fill="none" stroke="#F3EEE6" stroke-width="1.7" stroke-linecap="round"/>
				<circle cx="16" cy="14.6" r="1.45" fill="#C46A45"/>
			</svg>
			<span class="word">Kaya<span>Vue</span></span>
		</a>
		<nav class="nav-desktop" aria-label="Primary">
			<?php kayavue_primary_nav('nav-list'); ?>
		</nav>
		<form class="header-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
			<label class="screen-reader" for="kv-q">Search the guide</label>
			<input id="kv-q" type="search" name="s" placeholder="Search the guide" value="<?php echo esc_attr(get_search_query()); ?>">
			<button type="submit">Search</button>
		</form>
		<details class="nav-mobile">
			<summary>Menu</summary>
			<nav aria-label="Mobile">
				<?php kayavue_primary_nav('nav-list'); ?>
			</nav>
			<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
				<label class="screen-reader" for="kv-q-mobile">Search the guide</label>
				<input id="kv-q-mobile" type="search" name="s" placeholder="Search the guide" value="<?php echo esc_attr(get_search_query()); ?>">
				<button type="submit">Search</button>
			</form>
		</details>
	</div>
</header>
