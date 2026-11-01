<?php
/**
 * Clinic directory.
 *
 * @package KayaVue
 */

get_header();
$city_slug = kayavue_request_slug('city');
$current = $city_slug ? get_term_by('slug', $city_slug, 'kv_city') : null;
?>
<main id="content" class="section">
	<div class="wrap">
		<header class="page-hero">
			<p class="kicker">Directory</p>
			<h1><?php echo ($current && !is_wp_error($current)) ? esc_html('Clinics in ' . $current->name) : 'Clinics'; ?></h1>
			<p class="dek">Demo profiles with a city, a lead doctor, and the procedures they offer. A profile is not a recommendation.</p>
		</header>
		<form class="filter-bar" method="get" action="<?php echo esc_url(get_post_type_archive_link('kv_clinic')); ?>">
			<label for="clinic-city">City</label>
			<select id="clinic-city" name="city">
				<option value="">All cities</option>
				<?php foreach (kayavue_all_cities() as $city) : ?>
					<option value="<?php echo esc_attr($city->slug); ?>" <?php selected($city_slug, $city->slug); ?>><?php echo esc_html($city->name); ?></option>
				<?php endforeach; ?>
			</select>
			<button class="btn btn-clay" type="submit">Filter</button>
		</form>
		<?php if (have_posts()) : ?>
			<div class="clinic-list">
				<?php
                while (have_posts()) :
                    the_post();
                    get_template_part('template-parts/card', 'clinic', ['post' => get_post()]);
                endwhile;
                ?>
			</div>
			<?php the_posts_pagination(['mid_size' => 1]); ?>
		<?php else : ?>
			<?php kayavue_empty_state('No clinics in this filter', 'Try another city. Every metro in the demo has at least one profile.', [['All clinics', get_post_type_archive_link('kv_clinic')]]); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
