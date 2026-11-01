<?php
/**
 * Not found.
 *
 * @package KayaVue
 */

get_header();
?>
<main id="content" class="section">
	<div class="wrap">
		<?php kayavue_empty_state(
            'That page is not on KayaVue',
            'The address may be mistyped, or the demo has not published it. The directory itself is intact.',
            [
                ['Procedures', get_post_type_archive_link('kv_procedure') ?: home_url('/procedures/')],
                ['Cities', home_url('/cities/')],
                ['Home', home_url('/')],
            ]
        ); ?>
	</div>
</main>
<?php
get_footer();
