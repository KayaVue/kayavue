<?php
/**
 * Procedure catalogue.
 *
 * @package KayaVue
 */

get_header();
$concerns = get_terms(['taxonomy' => 'kv_concern', 'hide_empty' => true]);
?>
<main id="content" class="section">
	<div class="wrap">
		<header class="page-hero">
			<p class="kicker">Catalogue</p>
			<h1>Procedures</h1>
			<p class="dek">Education first: what the procedure is, a rupee range people ask about, and how recovery is usually described. Not a menu you can order from.</p>
		</header>
		<?php if ($concerns && !is_wp_error($concerns)) : ?>
			<nav class="concern-pills" aria-label="Filter by concern">
				<a href="<?php echo esc_url(get_post_type_archive_link('kv_procedure')); ?>" aria-current="page">All</a>
				<?php foreach ($concerns as $concern) : ?>
					<a href="<?php echo esc_url(get_term_link($concern)); ?>"><?php echo esc_html($concern->name); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
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
			<?php kayavue_empty_state('No procedures published', 'The catalogue is empty until the seeder runs.', [['Home', home_url('/')]]); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
