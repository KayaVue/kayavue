<?php
/**
 * Fallback loop.
 *
 * @package KayaVue
 */

get_header();
?>
<main id="content" class="section">
	<div class="wrap">
		<header class="page-hero">
			<p class="kicker">Guide</p>
			<h1><?php echo esc_html(wp_get_document_title()); ?></h1>
		</header>
		<?php if (have_posts()) : ?>
			<div class="stack">
				<?php
                while (have_posts()) :
                    the_post();
                    ?>
					<article <?php post_class('stack-item'); ?>>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php the_excerpt(); ?>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(['mid_size' => 1]); ?>
		<?php else : ?>
			<?php kayavue_empty_state('Nothing published yet', 'Once procedures and stories are seeded, they will gather here.', [['Home', home_url('/')]]); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
