<?php
/**
 * Template Name: Explore
 *
 * @package KayaVue
 */

get_header();
$procedure_slug = kayavue_request_slug('procedure');
$city_slug = kayavue_request_slug('city');
$procedure = kayavue_find_published('kv_procedure', $procedure_slug);
$city = $city_slug ? get_term_by('slug', $city_slug, 'kv_city') : null;
if ($city instanceof WP_Error) {
    $city = null;
}
$filtered = ($procedure instanceof WP_Post) || ($city instanceof WP_Term);
$clinics = $filtered ? kayavue_clinics([
    'procedure_id' => $procedure instanceof WP_Post ? $procedure->ID : 0,
    'city' => $city instanceof WP_Term ? $city->slug : '',
]) : [];
$stories = $filtered ? kayavue_reviews([
    'procedure_id' => $procedure instanceof WP_Post ? $procedure->ID : 0,
    'city' => $city instanceof WP_Term ? $city->slug : '',
    'limit' => 8,
]) : [];
?>
<main id="content" class="section">
	<div class="wrap">
		<header class="page-hero">
			<p class="kicker">Filter</p>
			<h1><?php echo esc_html(kayavue_explore_heading()); ?></h1>
		</header>
		<?php get_template_part('template-parts/search-band', null, [
            'procedure' => $procedure_slug,
            'city' => $city_slug,
            'heading' => 'Adjust the filter',
            'intro' => 'Procedure, city, or both. Keyword search lives in the header.',
        ]); ?>

		<?php if (!$filtered) : ?>
			<?php kayavue_empty_state(
                'Choose a procedure, a city, or both',
                'The directory is large enough that an unfiltered dump is a poor reading experience. Hair transplant in Mumbai, LASIK in Hyderabad, and clinics in Jaipur are all populated.',
                [
                    ['Hair transplant in Mumbai', home_url('/explore/?procedure=fue-hair-transplant&city=mumbai')],
                    ['All procedures', get_post_type_archive_link('kv_procedure')],
                ]
            ); ?>
		<?php else : ?>
			<section class="subsection">
				<div class="section-head">
					<h2>Clinics <?php echo $clinics ? '(' . esc_html((string) count($clinics)) . ')' : ''; ?></h2>
				</div>
				<?php if ($clinics) : ?>
					<div class="clinic-list">
						<?php foreach ($clinics as $clinic) : ?>
							<?php get_template_part('template-parts/card', 'clinic', ['post' => $clinic]); ?>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<?php kayavue_empty_state(
                        'No demo clinic matches',
                        'The procedure may not be listed in that city. Try the procedure alone, or another metro.',
                        [
                            ['Clear the city', $procedure instanceof WP_Post ? add_query_arg('procedure', $procedure->post_name, home_url('/explore/')) : home_url('/explore/')],
                            ['Browse clinics', get_post_type_archive_link('kv_clinic')],
                        ]
                    ); ?>
				<?php endif; ?>
			</section>
			<section class="subsection">
				<div class="section-head"><h2>Stories</h2></div>
				<?php if ($stories) : ?>
					<div class="review-grid">
						<?php foreach ($stories as $story) : ?>
							<?php get_template_part('template-parts/card', 'review', ['post' => $story]); ?>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<?php kayavue_empty_state('No stories in this slice', 'A clinic can still be listed without a review in the same filter.', []); ?>
				<?php endif; ?>
			</section>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
