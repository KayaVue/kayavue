<?php
/**
 * Homepage.
 *
 * @package KayaVue
 */

get_header();

$procedures = kayavue_all_procedures();
$featured = array_values(array_filter($procedures, static function ($procedure) {
    return get_post_meta($procedure->ID, 'kv_featured', true) === '1';
}));
$featured = array_slice($featured, 0, 6);
$rail = array_slice($procedures, 0, 5);
$clinics = kayavue_top_clinics(4);
$stories = kayavue_reviews(['limit' => 4]);
$pairs = [];
foreach (['fue-hair-transplant', 'rhinoplasty', 'dental-veneers'] as $slug) {
    $found = kayavue_find_published('kv_procedure', $slug);
    if ($found) {
        $pairs[] = $found;
    }
}
$procedure_count = wp_count_posts('kv_procedure');
$clinic_count = wp_count_posts('kv_clinic');
$review_count = wp_count_posts('kv_review');
?>
<main id="content">
	<section class="hero">
		<div class="wrap hero-grid">
			<div class="hero-copy">
				<p class="kicker">Patient guide · <?php echo esc_html(wp_date('F Y')); ?></p>
				<h1>Compare aesthetic care, city by city.</h1>
				<p class="dek">Read what a procedure involves, what private clinics in Indian cities often charge, and how recovery is usually described. KayaVue does not book surgery and does not give medical advice.</p>
				<p class="demo-line">You are looking at a demo. Clinic names, doctors, and reviews are invented so the directory can be browsed.</p>
				<dl class="hero-facts">
					<div><dt>Procedures</dt><dd><?php echo esc_html((string) (int) $procedure_count->publish); ?></dd></div>
					<div><dt>Clinics</dt><dd><?php echo esc_html((string) (int) $clinic_count->publish); ?></dd></div>
					<div><dt>Stories</dt><dd><?php echo esc_html((string) (int) $review_count->publish); ?></dd></div>
					<div><dt>Cities</dt><dd><?php echo esc_html((string) count(kayavue_all_cities())); ?></dd></div>
				</dl>
			</div>
			<aside class="hero-index" aria-label="A short index of procedures">
				<p class="kicker">In the index</p>
				<ol>
					<?php foreach ($rail as $procedure) : ?>
						<li>
							<a href="<?php echo esc_url(get_permalink($procedure)); ?>">
								<span><?php echo esc_html(get_the_title($procedure)); ?></span>
								<em><?php echo esc_html(kayavue_inr_range((int) get_post_meta($procedure->ID, 'kv_cost_min', true), (int) get_post_meta($procedure->ID, 'kv_cost_max', true))); ?></em>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
				<a class="text-link" href="<?php echo esc_url(get_post_type_archive_link('kv_procedure') ?: home_url('/procedures/')); ?>">All procedures</a>
			</aside>
		</div>
	</section>

	<div class="wrap band-wrap">
		<?php get_template_part('template-parts/search-band', null, [
            'heading' => 'Search by procedure and city',
            'intro' => 'See which demo clinics offer the procedure, and which stories sit in that city.',
        ]); ?>
	</div>

	<section class="section">
		<div class="wrap">
			<div class="section-head">
				<h2>Procedures people compare</h2>
				<p>Fee bands are wide on purpose. A quote is a conversation with a clinic, not a number on a homepage.</p>
			</div>
			<div class="proc-list">
				<?php $i = 1; foreach ($featured as $procedure) : ?>
					<?php get_template_part('template-parts/card', 'procedure', ['post' => $procedure, 'index' => $i]); ?>
					<?php $i++; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section-alt">
		<div class="wrap">
			<div class="section-head">
				<h2>Clinics with more than one story</h2>
				<a href="<?php echo esc_url(get_post_type_archive_link('kv_clinic') ?: home_url('/clinics/')); ?>">All clinics</a>
			</div>
			<div class="clinic-list">
				<?php foreach ($clinics as $clinic) : ?>
					<?php get_template_part('template-parts/card', 'clinic', ['post' => $clinic]); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="wrap">
			<div class="section-head">
				<h2>Recent stories</h2>
				<a href="<?php echo esc_url(get_post_type_archive_link('kv_review') ?: home_url('/reviews/')); ?>">All reviews</a>
			</div>
			<div class="review-grid">
				<?php foreach ($stories as $index => $story) : ?>
					<?php get_template_part('template-parts/card', 'review', ['post' => $story, 'featured' => $index === 0]); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section-alt">
		<div class="wrap">
			<div class="section-head">
				<h2>Diagrams, not promises</h2>
				<p>A few drawings so the vocabulary is clear. They are not patient photographs, and they are not a prediction of your result.</p>
			</div>
			<div class="pair-grid">
				<?php foreach ($pairs as $procedure) : ?>
					<?php $key = (string) get_post_meta($procedure->ID, 'kv_diagram', true); ?>
					<a class="pair" href="<?php echo esc_url(get_permalink($procedure)); ?>">
						<div class="pair-figs">
							<?php echo kayavue_diagram($key ?: 'face', 'before'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php echo kayavue_diagram($key ?: 'face', 'after'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<h3><?php echo esc_html(get_the_title($procedure)); ?></h3>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="wrap trust">
			<article>
				<h2>Independent of any clinic</h2>
				<p>A listing is not a recommendation. This demo does not sell placement.</p>
			</article>
			<article>
				<h2>Ranges, not quotations</h2>
				<p>Ask what the fee leaves out: GST, medicines, anaesthesia, garments, reviews.</p>
			</article>
			<article>
				<h2>Stories, not success rates</h2>
				<p>“Worth it for them” is how a writer answered. It is not an outcome study.</p>
			</article>
			<article>
				<h2>Results vary</h2>
				<p>If you need care, see someone who can examine you. Start with the <a href="<?php echo esc_url(home_url('/medical-disclaimer/')); ?>">disclaimer</a>.</p>
			</article>
		</div>
	</section>
</main>
<?php
get_footer();
