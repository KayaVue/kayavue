<?php
/**
 * Template Name: Cities
 *
 * @package KayaVue
 */

get_header();
?>
<main id="content" class="section">
	<div class="wrap">
		<?php
        while (have_posts()) :
            the_post();
            ?>
			<header class="page-hero">
				<p class="kicker">Places</p>
				<h1><?php the_title(); ?></h1>
				<?php if (has_excerpt()) : ?><p class="dek"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
			</header>
			<div class="prose prose-lead"><?php the_content(); ?></div>
		<?php endwhile; ?>
		<div class="city-index">
			<?php foreach (kayavue_all_cities() as $city) : ?>
				<?php
                $clinics = kayavue_clinics(['city' => $city->slug]);
                $story_count = count(kayavue_reviews(['city' => $city->slug]));
                ?>
				<a class="city-card" href="<?php echo esc_url(get_term_link($city)); ?>">
					<h2><?php echo esc_html($city->name); ?></h2>
					<p><?php echo esc_html($city->description); ?></p>
					<p class="city-count"><?php echo esc_html((string) count($clinics)); ?> <?php echo count($clinics) === 1 ? 'clinic' : 'clinics'; ?> · <?php echo esc_html((string) $story_count); ?> <?php echo $story_count === 1 ? 'story' : 'stories'; ?></p>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</main>
<?php
get_footer();
