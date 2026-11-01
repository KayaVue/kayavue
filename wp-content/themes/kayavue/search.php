<?php
/**
 * Keyword search across the directory.
 *
 * @package KayaVue
 */

get_header();
$term = get_search_query();
?>
<main id="content" class="section">
	<div class="wrap">
		<header class="page-hero">
			<p class="kicker">Search</p>
			<h1><?php echo $term !== '' ? 'Results for “' . esc_html($term) . '”' : 'Search the guide'; ?></h1>
		</header>
		<form class="filter-bar" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
			<label for="kv-search-page">Keywords</label>
			<input id="kv-search-page" type="search" name="s" value="<?php echo esc_attr($term); ?>">
			<button class="btn btn-clay" type="submit">Search</button>
		</form>
		<?php if (have_posts()) : ?>
			<div class="result-list">
				<?php
                while (have_posts()) :
                    the_post();
                    $type = get_post_type_object(get_post_type());
                    ?>
					<article class="result-row">
						<p class="kicker"><?php echo esc_html($type ? $type->labels->singular_name : 'Page'); ?></p>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: wp_strip_all_tags(get_the_content()), 32, '…')); ?></p>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(['mid_size' => 1]); ?>
		<?php else : ?>
			<?php kayavue_empty_state(
                'No pages use those words',
                'Try a procedure name, a city, or a clinic. Hair, rhinoplasty, Jaipur, and LASIK all have pages in this demo.',
                [['Browse procedures', get_post_type_archive_link('kv_procedure') ?: home_url('/procedures/')]]
            ); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
