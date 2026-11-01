<?php
/**
 * Procedures in one concern.
 *
 * @package KayaVue
 */

get_header();
$term = get_queried_object();
?>
<main id="content" class="section">
	<div class="wrap">
		<header class="page-hero">
			<p class="kicker"><a href="<?php echo esc_url(get_post_type_archive_link('kv_procedure')); ?>">Procedures</a></p>
			<h1><?php echo esc_html($term->name); ?></h1>
			<?php if ($term->description) : ?>
				<p class="dek"><?php echo esc_html($term->description); ?></p>
			<?php endif; ?>
		</header>
		<nav class="concern-pills" aria-label="Filter by concern">
			<a href="<?php echo esc_url(get_post_type_archive_link('kv_procedure')); ?>">All</a>
			<?php
            $concerns = get_terms(['taxonomy' => 'kv_concern', 'hide_empty' => true]);
            if ($concerns && !is_wp_error($concerns)) :
                foreach ($concerns as $concern) :
                    ?>
					<a href="<?php echo esc_url(get_term_link($concern)); ?>" <?php echo $concern->term_id === $term->term_id ? 'aria-current="page"' : ''; ?>><?php echo esc_html($concern->name); ?></a>
					<?php
                endforeach;
            endif;
            ?>
		</nav>
		<?php if (have_posts()) : ?>
			<div class="proc-list">
				<?php
                $i = 1;
                while (have_posts()) :
                    the_post();
                    get_template_part('template-parts/card', 'procedure', ['post' => get_post(), 'index' => $i]);
                    $i++;
                endwhile;
                ?>
			</div>
		<?php else : ?>
			<?php kayavue_empty_state('Nothing under this concern', 'Try the full catalogue.', [['All procedures', get_post_type_archive_link('kv_procedure')]]); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
