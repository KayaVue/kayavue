<?php
/**
 * Review card.
 *
 * @package KayaVue
 *
 * @var array{post?:WP_Post,featured?:bool} $args
 */

$review = $args['post'];
$featured = !empty($args['featured']);
if (!isset($review->kv_rating)) {
    $review->kv_rating = (int) get_post_meta($review->ID, 'kv_rating', true);
    $review->kv_worth = (string) get_post_meta($review->ID, 'kv_worth', true);
    $review->kv_procedure_id = (int) get_post_meta($review->ID, 'kv_procedure_id', true);
    $review->kv_clinic_id = (int) get_post_meta($review->ID, 'kv_clinic_id', true);
    $review->kv_reviewer = (string) get_post_meta($review->ID, 'kv_reviewer', true);
    $review->kv_age_band = (string) get_post_meta($review->ID, 'kv_age_band', true);
    $review->kv_when = (string) get_post_meta($review->ID, 'kv_when', true);
}
$procedure = get_post((int) $review->kv_procedure_id);
$clinic = get_post((int) $review->kv_clinic_id);
$city = kayavue_primary_term($review->ID, 'kv_city');
?>
<article class="review-card<?php echo $featured ? ' is-featured' : ''; ?>">
	<p class="kicker"><?php echo esc_html(kayavue_worth_label((string) $review->kv_worth)); ?></p>
	<h3><a href="<?php echo esc_url(get_permalink($review)); ?>"><?php echo esc_html(get_the_title($review)); ?></a></h3>
	<p class="review-rating"><?php echo kayavue_stars_markup($review->kv_rating); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- local SVG, no user input. ?> <span><?php echo esc_html((string) (int) $review->kv_rating); ?> of 5</span></p>
	<p class="review-excerpt"><?php echo esc_html(wp_trim_words(wp_strip_all_tags($review->post_content), $featured ? 42 : 28, '…')); ?></p>
	<p class="byline">
		<?php echo esc_html((string) $review->kv_reviewer); ?>
		· <?php echo esc_html((string) $review->kv_age_band); ?>
		<?php if ($city) : ?> · <?php echo esc_html($city->name); ?><?php endif; ?>
		· <?php echo esc_html((string) $review->kv_when); ?>
	</p>
	<p class="review-links">
		<?php if ($procedure instanceof WP_Post) : ?>
			<a href="<?php echo esc_url(get_permalink($procedure)); ?>"><?php echo esc_html(get_the_title($procedure)); ?></a>
		<?php endif; ?>
		<?php if ($clinic instanceof WP_Post) : ?>
			<a href="<?php echo esc_url(get_permalink($clinic)); ?>"><?php echo esc_html(get_the_title($clinic)); ?></a>
		<?php endif; ?>
	</p>
</article>
