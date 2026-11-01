<?php
/**
 * Clinic profile.
 *
 * @package KayaVue
 */

get_header();
while (have_posts()) :
    the_post();
    $clinic_id = get_the_ID();
    $city = kayavue_primary_term($clinic_id, 'kv_city');
    $stats = kayavue_stats(kayavue_reviews(['clinic_id' => $clinic_id]));
    $procedures = kayavue_clinic_procedures($clinic_id);
    $stories = kayavue_reviews(['clinic_id' => $clinic_id, 'limit' => 8]);
    ?>
<main id="content" class="section">
	<div class="wrap">
		<p class="crumbs">
			<a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
			<span aria-hidden="true">/</span>
			<a href="<?php echo esc_url(get_post_type_archive_link('kv_clinic')); ?>">Clinics</a>
			<?php if ($city) : ?>
				<span aria-hidden="true">/</span>
				<a href="<?php echo esc_url(get_term_link($city)); ?>"><?php echo esc_html($city->name); ?></a>
			<?php endif; ?>
		</p>
		<header class="page-hero">
			<p class="kicker">Demo profile<?php echo $city ? ' · ' . esc_html($city->name) : ''; ?></p>
			<h1><?php the_title(); ?></h1>
			<p class="dek"><?php echo esc_html((string) get_post_meta($clinic_id, 'kv_area', true)); ?> · <?php echo esc_html((string) get_post_meta($clinic_id, 'kv_focus', true)); ?></p>
		</header>
		<div class="layout">
			<article>
				<div class="prose"><?php the_content(); ?></div>
				<?php if ($procedures) : ?>
					<h2 class="subsection-title">Procedures listed</h2>
					<ul class="offer-list">
						<?php foreach ($procedures as $procedure) : ?>
							<li>
								<a href="<?php echo esc_url(get_permalink($procedure)); ?>"><?php echo esc_html(get_the_title($procedure)); ?></a>
								<span><?php echo esc_html(kayavue_inr_range((int) get_post_meta($procedure->ID, 'kv_cost_min', true), (int) get_post_meta($procedure->ID, 'kv_cost_max', true))); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</article>
			<aside class="doctor-card">
				<p class="kicker">Lead doctor, sample</p>
				<h2><?php echo esc_html((string) get_post_meta($clinic_id, 'kv_doctor', true)); ?></h2>
				<p><?php echo esc_html((string) get_post_meta($clinic_id, 'kv_qualifications', true)); ?></p>
				<dl>
					<div><dt>Focus</dt><dd><?php echo esc_html((string) get_post_meta($clinic_id, 'kv_focus', true)); ?></dd></div>
					<div><dt>On this demo since</dt><dd><?php echo esc_html((string) get_post_meta($clinic_id, 'kv_established', true)); ?></dd></div>
					<div><dt>Address</dt><dd><?php echo esc_html((string) get_post_meta($clinic_id, 'kv_address', true)); ?></dd></div>
					<?php if ($city) : ?>
						<div><dt>City</dt><dd><a href="<?php echo esc_url(get_term_link($city)); ?>"><?php echo esc_html($city->name); ?></a></dd></div>
					<?php endif; ?>
				</dl>
				<p class="fine">Fictional profile. Not a real clinic, and not a person you can book. No phone number is published on purpose.</p>
				<?php if ($stats['count']) : ?>
					<p class="clinic-score clinic-score-block">
						<?php echo kayavue_stars_markup($stats['avg']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- local SVG, no user input. ?>
						<strong><?php echo esc_html(number_format((float) $stats['avg'], 1)); ?></strong>
						<em><?php echo esc_html(kayavue_worth_sentence($stats)); ?></em>
					</p>
				<?php endif; ?>
			</aside>
		</div>
		<section class="subsection">
			<div class="section-head">
				<h2>Stories about this clinic</h2>
			</div>
			<?php if ($stories) : ?>
				<div class="review-grid">
					<?php foreach ($stories as $story) : ?>
						<?php get_template_part('template-parts/card', 'review', ['post' => $story]); ?>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<?php kayavue_empty_state('No stories for this clinic yet', 'The profile can still tell you the city and the procedure list.', []); ?>
			<?php endif; ?>
		</section>
	</div>
</main>
	<?php
endwhile;
get_footer();
