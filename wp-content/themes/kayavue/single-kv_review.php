<?php
/**
 * Single review.
 *
 * @package KayaVue
 */

get_header();
while (have_posts()) :
    the_post();
    $review_id = get_the_ID();
    $rating = (int) get_post_meta($review_id, 'kv_rating', true);
    $worth = (string) get_post_meta($review_id, 'kv_worth', true);
    $procedure = get_post((int) get_post_meta($review_id, 'kv_procedure_id', true));
    $clinic = get_post((int) get_post_meta($review_id, 'kv_clinic_id', true));
    $city = kayavue_primary_term($review_id, 'kv_city');
    ?>
<main id="content" class="section">
	<div class="wrap-narrow">
		<p class="crumbs">
			<a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
			<span aria-hidden="true">/</span>
			<a href="<?php echo esc_url(get_post_type_archive_link('kv_review')); ?>">Reviews</a>
		</p>
		<article>
			<header class="page-hero">
				<p class="kicker">Demo story<?php echo $city ? ' · ' . esc_html($city->name) : ''; ?></p>
				<h1><?php the_title(); ?></h1>
				<p class="review-rating review-rating-lg"><?php echo kayavue_stars_markup($rating); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- local SVG, no user input. ?> <span><?php echo esc_html((string) $rating); ?> of 5 · <?php echo esc_html(kayavue_worth_label($worth)); ?></span></p>
				<p class="byline">
					<?php echo esc_html((string) get_post_meta($review_id, 'kv_reviewer', true)); ?>
					· <?php echo esc_html((string) get_post_meta($review_id, 'kv_age_band', true)); ?>
					· <?php echo esc_html((string) get_post_meta($review_id, 'kv_when', true)); ?>
					· <?php echo esc_html(get_the_date()); ?>
				</p>
			</header>
			<div class="prose"><?php the_content(); ?></div>
			<p class="fine">Fictional demo story, written so the review page has something honest to hold. Not a real patient. Not medical advice. Results vary.</p>
			<ul class="link-cards">
				<?php if ($procedure instanceof WP_Post) : ?>
					<li><a href="<?php echo esc_url(get_permalink($procedure)); ?>"><span>Procedure</span><strong><?php echo esc_html(get_the_title($procedure)); ?></strong></a></li>
				<?php endif; ?>
				<?php if ($clinic instanceof WP_Post) : ?>
					<li><a href="<?php echo esc_url(get_permalink($clinic)); ?>"><span>Clinic</span><strong><?php echo esc_html(get_the_title($clinic)); ?></strong></a></li>
				<?php endif; ?>
				<?php if ($city) : ?>
					<li><a href="<?php echo esc_url(get_term_link($city)); ?>"><span>City</span><strong><?php echo esc_html($city->name); ?></strong></a></li>
				<?php endif; ?>
			</ul>
		</article>
	</div>
</main>
	<?php
endwhile;
get_footer();
