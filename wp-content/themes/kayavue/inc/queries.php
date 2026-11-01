<?php
/**
 * Main-query filters for archives and search.
 *
 * @package KayaVue
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('pre_get_posts', 'kayavue_filter_main_query');

/**
 * @param WP_Query $query Main or secondary query.
 */
function kayavue_filter_main_query($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->is_search()) {
        $query->set('post_type', ['kv_procedure', 'kv_clinic', 'kv_review', 'page']);
        $query->set('posts_per_page', 12);
        return;
    }

    if ($query->is_post_type_archive('kv_procedure')) {
        $query->set('orderby', ['menu_order' => 'ASC', 'title' => 'ASC']);
        $query->set('posts_per_page', 24);
    }

    if ($query->is_post_type_archive('kv_clinic')) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', 24);
        $city = kayavue_request_slug('city');
        if ($city !== '') {
            $query->set('tax_query', [[
                'taxonomy' => 'kv_city',
                'field' => 'slug',
                'terms' => $city,
            ]]);
        }
    }

    if ($query->is_post_type_archive('kv_review')) {
        $query->set('posts_per_page', 12);
        $tax = [];
        $meta = [];
        $city = kayavue_request_slug('city');
        if ($city !== '') {
            $tax[] = [
                'taxonomy' => 'kv_city',
                'field' => 'slug',
                'terms' => $city,
            ];
        }
        $procedure = kayavue_find_published('kv_procedure', kayavue_request_slug('procedure'));
        if ($procedure) {
            $meta[] = [
                'key' => 'kv_procedure_id',
                'value' => (string) $procedure->ID,
            ];
        }
        $clinic = kayavue_find_published('kv_clinic', kayavue_request_slug('clinic'));
        if ($clinic) {
            $meta[] = [
                'key' => 'kv_clinic_id',
                'value' => (string) $clinic->ID,
            ];
        }
        if ($tax) {
            $query->set('tax_query', $tax);
        }
        if ($meta) {
            $query->set('meta_query', $meta);
        }
    }

    if ($query->is_tax('kv_concern')) {
        $query->set('post_type', 'kv_procedure');
        $query->set('orderby', ['menu_order' => 'ASC', 'title' => 'ASC']);
        $query->set('posts_per_page', 24);
    }

    if ($query->is_tax('kv_city')) {
        $query->set('post_type', ['kv_clinic', 'kv_review']);
        $query->set('posts_per_page', 24);
    }
}
