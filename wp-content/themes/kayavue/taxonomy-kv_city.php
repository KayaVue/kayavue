<?php
/**
 * One city: clinics and stories.
 *
 * @package KayaVue
 */

get_header();
$city = get_queried_object();
$clinics = kayavue_clinics(['city' => $city->slug]);
$stories = kayavue_reviews(['city' => $city->slug, 'limit' => 6]);
?>
<main id="content" class="section">
	<div class="wrap">
		<p class="crumbs">
			<a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
			<span aria-hidden="true">/</span>
			<a href="<?php echo esc_url(home_url('/cities/')); ?>">Cities</a>
		</p>
		<header class="page-hero">
			<p class="kicker">City</p>
			<h1><?php echo esc_html($city->name); ?></h1>
			<?php if ($city->description) : ?><p class="dek"><?php echo esc_html($city->description); ?></p><?php endif; ?>
		</header>
		<section class="subsection">
			<div class="section-head">
				<h2>Clinics</h2>
				<a href="<?php echo esc_url(add_query_arg('city', $city->slug, get_post_type_archive_link('kv_clinic'))); ?>">Directory filter</a>
			</div>
			<?php if ($clinics) : ?>
				<div class="clinic-list">
					<?php foreach ($clinics as $clinic) : ?>
						<?php get_template_part('template-parts/card', 'clinic', ['post' => $clinic]); ?>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<?php kayavue_empty_state('No clinics in ' . $city->name . ' yet', 'The city is on the map. The directory does not invent a clinic to fill it.', [['All cities', home_url('/cities/')]]); ?>
			<?php endif; ?>
		</section>
		<section class="subsection">
			<div class="section-head">
				<h2>Stories</h2>
			</div>
			<?php if ($stories) : ?>
				<div class="review-grid">
					<?php foreach ($stories as $story) : ?>
						<?php get_template_part('template-parts/card', 'review', ['post' => $story]); ?>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<?php kayavue_empty_state('No stories from ' . $city->name . ' yet', 'Clinics can still be read without a review attached.', []); ?>
			<?php endif; ?>
		</section>
	</div>
</main>
<?php
get_footer();
