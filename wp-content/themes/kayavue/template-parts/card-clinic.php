<?php
/**
 * Clinic row.
 *
 * @package KayaVue
 *
 * @var array{post?:WP_Post} $args
 */

$clinic = $args['post'];
$city = kayavue_primary_term($clinic->ID, 'kv_city');
$stats = kayavue_stats(kayavue_reviews(['clinic_id' => $clinic->ID]));
$area = (string) get_post_meta($clinic->ID, 'kv_area', true);
$doctor = (string) get_post_meta($clinic->ID, 'kv_doctor', true);
$focus = (string) get_post_meta($clinic->ID, 'kv_focus', true);
$abbr = $city ? strtoupper(substr(remove_accents($city->name), 0, 3)) : '—';
?>
<a class="clinic-row" href="<?php echo esc_url(get_permalink($clinic)); ?>">
	<span class="city-abbr" aria-hidden="true"><?php echo esc_html($abbr); ?></span>
	<span class="clinic-main">
		<span class="clinic-title"><?php echo esc_html(get_the_title($clinic)); ?></span>
		<span class="clinic-meta"><?php echo esc_html(trim($area . ($city ? ' · ' . $city->name : ''))); ?></span>
		<span class="clinic-meta"><?php echo esc_html($doctor); ?><?php echo $focus ? ' · ' . esc_html($focus) : ''; ?></span>
	</span>
	<span class="clinic-score">
		<?php if ($stats['count']) : ?>
			<?php echo kayavue_stars_markup($stats['avg']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- local SVG, no user input. ?>
			<strong><?php echo esc_html(number_format((float) $stats['avg'], 1)); ?></strong>
			<em><?php echo esc_html((string) $stats['count']); ?> <?php echo esc_html($stats['count'] === 1 ? 'story' : 'stories'); ?></em>
		<?php else : ?>
			<em>No stories yet</em>
		<?php endif; ?>
	</span>
</a>
