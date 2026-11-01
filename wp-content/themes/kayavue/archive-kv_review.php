<?php
/**
 * Review archive with procedure and city filters.
 *
 * @package KayaVue
 */

get_header();
$city_slug = kayavue_request_slug('city');
$procedure_slug = kayavue_request_slug('procedure');
?>
<main id="content" class="section">
	<div class="wrap">
		<header class="page-hero">
			<p class="kicker">Stories</p>
			<h1>Reviews</h1>
			<p class="dek">Short fictional accounts tied to a procedure, a clinic, and a city. Stars are a rating. “Worth it” is the writer’s own answer, not a medical result.</p>
		</header>
		<form class="filter-bar filter-bar-wide" method="get" action="<?php echo esc_url(get_post_type_archive_link('kv_review')); ?>">
			<div class="field">
				<label for="review-procedure">Procedure</label>
				<select id="review-procedure" name="procedure">
					<option value="">Any procedure</option>
					<?php foreach (kayavue_all_procedures() as $procedure) : ?>
						<option value="<?php echo esc_attr($procedure->post_name); ?>" <?php selected($procedure_slug, $procedure->post_name); ?>><?php echo esc_html(get_the_title($procedure)); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="field">
				<label for="review-city">City</label>
				<select id="review-city" name="city">
					<option value="">Any city</option>
					<?php foreach (kayavue_all_cities() as $city) : ?>
						<option value="<?php echo esc_attr($city->slug); ?>" <?php selected($city_slug, $city->slug); ?>><?php echo esc_html($city->name); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<button class="btn btn-clay" type="submit">Filter</button>
		</form>
		<?php if (have_posts()) : ?>
			<div class="review-grid">
				<?php
                while (have_posts()) :
                    the_post();
                    get_template_part('template-parts/card', 'review', ['post' => get_post()]);
                endwhile;
                ?>
			</div>
			<?php the_posts_pagination(['mid_size' => 1]); ?>
		<?php else : ?>
			<?php kayavue_empty_state('No stories in this filter', 'Clear it, or pick a procedure that has several cities — hair transplant and rhinoplasty do.', [['All reviews', get_post_type_archive_link('kv_review')]]); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
