<?php
/**
 * Single procedure.
 *
 * @package KayaVue
 */

get_header();
while (have_posts()) :
    the_post();
    $procedure_id = get_the_ID();
    $stats = kayavue_stats(kayavue_reviews(['procedure_id' => $procedure_id]));
    $concern = kayavue_primary_term($procedure_id, 'kv_concern');
    $city_slug = kayavue_request_slug('city');
    $clinics = kayavue_clinics([
        'procedure_id' => $procedure_id,
        'city' => $city_slug,
    ]);
    $stories = kayavue_reviews(['procedure_id' => $procedure_id, 'limit' => 6]);
    $related = kayavue_related_procedures(get_post(), 3);
    $diagram = (string) get_post_meta($procedure_id, 'kv_diagram', true);
    $facts = [
        'Fee range' => kayavue_inr_range((int) get_post_meta($procedure_id, 'kv_cost_min', true), (int) get_post_meta($procedure_id, 'kv_cost_max', true)),
        'Basis' => (string) get_post_meta($procedure_id, 'kv_cost_basis', true),
        'Recovery' => (string) get_post_meta($procedure_id, 'kv_recovery', true),
        'Time away' => (string) get_post_meta($procedure_id, 'kv_downtime', true),
        'Anaesthesia' => (string) get_post_meta($procedure_id, 'kv_anesthesia', true),
        'Visits' => (string) get_post_meta($procedure_id, 'kv_sessions', true),
    ];
    ?>
<main id="content" class="section">
	<div class="wrap">
		<p class="crumbs">
			<a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
			<span aria-hidden="true">/</span>
			<a href="<?php echo esc_url(get_post_type_archive_link('kv_procedure')); ?>">Procedures</a>
			<?php if ($concern) : ?>
				<span aria-hidden="true">/</span>
				<a href="<?php echo esc_url(get_term_link($concern)); ?>"><?php echo esc_html($concern->name); ?></a>
			<?php endif; ?>
		</p>
		<header class="page-hero">
			<?php if ($concern) : ?><p class="kicker"><?php echo esc_html($concern->name); ?></p><?php endif; ?>
			<h1><?php the_title(); ?></h1>
			<?php if (has_excerpt()) : ?><p class="dek"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
		</header>
		<dl class="stat-grid">
			<?php foreach ($facts as $label => $value) : ?>
				<?php if ($value !== '') : ?>
					<div>
						<dt><?php echo esc_html($label); ?></dt>
						<dd><?php echo esc_html($value); ?></dd>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</dl>
		<p class="fine">These are broad private-clinic ranges people in Indian cities ask about. They are not a quotation. GST, medicines, and follow-up may sit outside them.</p>
		<?php get_template_part('template-parts/worth', null, ['stats' => $stats]); ?>
		<div class="layout">
			<article class="prose">
				<?php the_content(); ?>
			</article>
			<aside class="side-card">
				<h2>A drawing, for the vocabulary</h2>
				<p>Not a patient. Not a forecast.</p>
				<?php echo kayavue_diagram($diagram ?: 'face', 'before'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo kayavue_diagram($diagram ?: 'face', 'after'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</aside>
		</div>

		<section class="subsection">
			<div class="section-head">
				<h2>Clinics that list it</h2>
			</div>
			<form class="filter-bar" method="get" action="<?php echo esc_url(get_permalink()); ?>">
				<label for="proc-city">City</label>
				<select id="proc-city" name="city">
					<option value="">All cities</option>
					<?php foreach (kayavue_all_cities() as $city) : ?>
						<option value="<?php echo esc_attr($city->slug); ?>" <?php selected($city_slug, $city->slug); ?>><?php echo esc_html($city->name); ?></option>
					<?php endforeach; ?>
				</select>
				<button class="btn btn-clay" type="submit">Filter</button>
			</form>
			<?php if ($clinics) : ?>
				<div class="clinic-list">
					<?php foreach ($clinics as $clinic) : ?>
						<?php get_template_part('template-parts/card', 'clinic', ['post' => $clinic]); ?>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<?php
                $city_term = $city_slug ? get_term_by('slug', $city_slug, 'kv_city') : null;
                $city_name = ($city_term && !is_wp_error($city_term)) ? $city_term->name : 'that city';
                kayavue_empty_state(
                    'No demo clinic in ' . $city_name . ' lists this',
                    'That is a finished answer, not an error. Look at other cities, or read the procedure notes without a local listing.',
                    [
                        ['All cities for this procedure', get_permalink()],
                        ['All clinics', get_post_type_archive_link('kv_clinic')],
                    ]
                );
                ?>
			<?php endif; ?>
		</section>

		<section class="subsection">
			<div class="section-head">
				<h2>Stories</h2>
				<a href="<?php echo esc_url(add_query_arg('procedure', get_post_field('post_name', $procedure_id), get_post_type_archive_link('kv_review'))); ?>">All stories for this procedure</a>
			</div>
			<?php if ($stories) : ?>
				<div class="review-grid">
					<?php foreach ($stories as $story) : ?>
						<?php get_template_part('template-parts/card', 'review', ['post' => $story]); ?>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<?php kayavue_empty_state('No stories for this procedure yet', 'The education above does not depend on a review.', []); ?>
			<?php endif; ?>
		</section>

		<?php if ($related) : ?>
			<section class="subsection">
				<div class="section-head"><h2>Nearby in the catalogue</h2></div>
				<div class="proc-list">
					<?php foreach ($related as $item) : ?>
						<?php get_template_part('template-parts/card', 'procedure', ['post' => $item]); ?>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>
	</div>
</main>
	<?php
endwhile;
get_footer();
