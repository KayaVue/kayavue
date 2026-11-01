<?php
/**
 * Self-reported worth-it summary.
 *
 * @package KayaVue
 *
 * @var array{stats?:array<string,mixed>} $args
 */

$stats = isset($args['stats']) ? $args['stats'] : ['count' => 0, 'avg' => null, 'worth_pct' => null, 'yes' => 0, 'no' => 0, 'unsure' => 0];
$count = (int) $stats['count'];
?>
<section class="worth-block">
	<?php if ($count === 0) : ?>
		<p class="kicker">Stories</p>
		<p class="worth-empty">No demo stories are attached yet. The fee range and the recovery notes still stand on their own.</p>
	<?php else : ?>
		<div class="worth-figure">
			<p class="worth-num"><?php echo esc_html((string) $stats['worth_pct']); ?><span>%</span></p>
			<p><?php echo esc_html(kayavue_worth_sentence($stats)); ?></p>
		</div>
		<div class="worth-side">
			<p class="avg"><?php echo kayavue_stars_markup($stats['avg']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- local SVG, no user input. ?> <strong><?php echo esc_html(number_format((float) $stats['avg'], 1)); ?></strong> average of <?php echo esc_html((string) $count); ?></p>
			<?php
            $yes_w = $count ? (100 * (int) $stats['yes'] / $count) : 0;
            $unsure_w = $count ? (100 * (int) $stats['unsure'] / $count) : 0;
            $no_w = $count ? (100 * (int) $stats['no'] / $count) : 0;
            ?>
			<div class="split" role="img" aria-label="<?php echo esc_attr(sprintf('%d said worth it, %d unsure, %d said not', (int) $stats['yes'], (int) $stats['unsure'], (int) $stats['no'])); ?>">
				<?php if ($yes_w > 0) : ?><span class="split-yes" style="width:<?php echo esc_attr((string) $yes_w); ?>%"></span><?php endif; ?>
				<?php if ($unsure_w > 0) : ?><span class="split-unsure" style="width:<?php echo esc_attr((string) $unsure_w); ?>%"></span><?php endif; ?>
				<?php if ($no_w > 0) : ?><span class="split-no" style="width:<?php echo esc_attr((string) $no_w); ?>%"></span><?php endif; ?>
			</div>
			<ul class="split-legend">
				<li><i class="swatch swatch-yes"></i> Worth it for them · <?php echo esc_html((string) $stats['yes']); ?></li>
				<li><i class="swatch swatch-unsure"></i> Unsure · <?php echo esc_html((string) $stats['unsure']); ?></li>
				<li><i class="swatch swatch-no"></i> Not worth it for them · <?php echo esc_html((string) $stats['no']); ?></li>
			</ul>
		</div>
	<?php endif; ?>
</section>
