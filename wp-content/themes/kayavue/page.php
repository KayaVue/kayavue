<?php
/**
 * Static pages.
 *
 * @package KayaVue
 */

get_header();
?>
<main id="content" class="section">
	<div class="wrap-narrow">
		<?php
        while (have_posts()) :
            the_post();
            ?>
			<article <?php post_class(); ?>>
				<header class="page-hero">
					<p class="kicker">KayaVue</p>
					<h1><?php the_title(); ?></h1>
					<?php if (has_excerpt()) : ?>
						<p class="dek"><?php echo esc_html(get_the_excerpt()); ?></p>
					<?php endif; ?>
				</header>
				<div class="prose"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	</div>
</main>
<?php
get_footer();
