<?php
/**
 * Procedure and city filter.
 *
 * @package KayaVue
 *
 * @var array{procedure?:string,city?:string,action?:string,heading?:string,intro?:string} $args
 */

$procedure_slug = isset($args['procedure']) ? (string) $args['procedure'] : kayavue_request_slug('procedure');
$city_slug = isset($args['city']) ? (string) $args['city'] : kayavue_request_slug('city');
$action = isset($args['action']) ? (string) $args['action'] : home_url('/explore/');
$heading = isset($args['heading']) ? (string) $args['heading'] : 'Find a procedure in a city';
$intro = isset($args['intro']) ? (string) $args['intro'] : 'Both fields can stay open. A city alone lists every demo clinic there.';
?>
<form class="search-band" method="get" action="<?php echo esc_url($action); ?>">
	<div class="search-copy">
		<h2><?php echo esc_html($heading); ?></h2>
		<p><?php echo esc_html($intro); ?></p>
	</div>
	<div class="search-grid">
		<div class="field">
			<label for="kv-procedure">Procedure</label>
			<select id="kv-procedure" name="procedure">
				<option value="">Any procedure</option>
				<?php foreach (kayavue_all_procedures() as $procedure) : ?>
					<option value="<?php echo esc_attr($procedure->post_name); ?>" <?php selected($procedure_slug, $procedure->post_name); ?>>
						<?php echo esc_html($procedure->post_title); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="field">
			<label for="kv-city">City</label>
			<select id="kv-city" name="city">
				<option value="">Any city</option>
				<?php foreach (kayavue_all_cities() as $city) : ?>
					<option value="<?php echo esc_attr($city->slug); ?>" <?php selected($city_slug, $city->slug); ?>>
						<?php echo esc_html($city->name); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<button class="btn btn-paper" type="submit">Show matches</button>
	</div>
</form>
