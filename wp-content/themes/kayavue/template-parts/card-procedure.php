<?php
/**
 * Procedure row.
 *
 * @package KayaVue
 *
 * @var array{post?:WP_Post,index?:int} $args
 */

$procedure = $args['post'];
$index = isset($args['index']) ? (int) $args['index'] : 0;
$stats = kayavue_stats(kayavue_reviews(['procedure_id' => $procedure->ID]));
$concern = kayavue_primary_term($procedure->ID, 'kv_concern');
$min = (int) get_post_meta($procedure->ID, 'kv_cost_min', true);
$max = (int) get_post_meta($procedure->ID, 'kv_cost_max', true);
?>
<a class="proc-row <?php echo esc_attr(kayavue_tone_class($procedure->ID)); ?>" href="<?php echo esc_url(get_permalink($procedure)); ?>">
	<?php if ($index) : ?>
		<span class="proc-num"><?php echo esc_html(sprintf('%02d', $index)); ?></span>
	<?php endif; ?>
	<span class="proc-main">
		<span class="proc-title"><?php echo esc_html(get_the_title($procedure)); ?></span>
		<span class="proc-meta">
			<?php if ($concern) : ?><span class="pill"><?php echo esc_html($concern->name); ?></span><?php endif; ?>
			<?php if ($stats['count']) : ?>
				<span><?php echo esc_html((string) $stats['worth_pct']); ?>% said worth it for them</span>
			<?php else : ?>
				<span>No stories yet</span>
			<?php endif; ?>
		</span>
	</span>
	<span class="proc-price">
		<strong><?php echo esc_html(kayavue_inr_range($min, $max)); ?></strong>
	</span>
</a>
