<?php
/**
 * Content types for the KayaVue directory.
 *
 * @package KayaVue
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'kayavue_register_content_types', 5);

/**
 * Register the directory post types and taxonomies.
 */
function kayavue_register_content_types() {
    register_taxonomy('kv_city', ['kv_clinic', 'kv_review'], [
        'labels' => [
            'name' => 'Cities',
            'singular_name' => 'City',
            'search_items' => 'Search cities',
            'all_items' => 'All cities',
            'edit_item' => 'Edit city',
            'add_new_item' => 'Add city',
        ],
        'public' => true,
        'hierarchical' => false,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'city', 'with_front' => false],
    ]);

    register_taxonomy('kv_concern', ['kv_procedure'], [
        'labels' => [
            'name' => 'Concerns',
            'singular_name' => 'Concern',
            'search_items' => 'Search concerns',
            'all_items' => 'All concerns',
            'edit_item' => 'Edit concern',
            'add_new_item' => 'Add concern',
        ],
        'public' => true,
        'hierarchical' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'concern', 'with_front' => false],
    ]);

    register_post_type('kv_procedure', [
        'labels' => [
            'name' => 'Procedures',
            'singular_name' => 'Procedure',
            'add_new_item' => 'Add procedure',
            'edit_item' => 'Edit procedure',
            'view_item' => 'View procedure',
            'search_items' => 'Search procedures',
            'not_found' => 'No procedures found',
        ],
        'public' => true,
        'has_archive' => 'procedures',
        'menu_icon' => 'dashicons-heart',
        'menu_position' => 21,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'procedures', 'with_front' => false],
        'supports' => ['title', 'editor', 'excerpt', 'revisions', 'page-attributes'],
        'taxonomies' => ['kv_concern'],
    ]);

    register_post_type('kv_clinic', [
        'labels' => [
            'name' => 'Clinics',
            'singular_name' => 'Clinic',
            'add_new_item' => 'Add clinic',
            'edit_item' => 'Edit clinic',
            'view_item' => 'View clinic',
            'search_items' => 'Search clinics',
            'not_found' => 'No clinics found',
        ],
        'public' => true,
        'has_archive' => 'clinics',
        'menu_icon' => 'dashicons-building',
        'menu_position' => 22,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'clinics', 'with_front' => false],
        'supports' => ['title', 'editor', 'excerpt', 'revisions'],
    ]);

    register_post_type('kv_review', [
        'labels' => [
            'name' => 'Reviews',
            'singular_name' => 'Review',
            'add_new_item' => 'Add review',
            'edit_item' => 'Edit review',
            'view_item' => 'View review',
            'search_items' => 'Search reviews',
            'not_found' => 'No reviews found',
        ],
        'public' => true,
        'has_archive' => 'reviews',
        'menu_icon' => 'dashicons-format-quote',
        'menu_position' => 23,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'reviews', 'with_front' => false],
        'supports' => ['title', 'editor', 'excerpt', 'revisions'],
    ]);
}
