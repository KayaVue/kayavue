<?php
/**
 * KayaVue theme setup.
 *
 * @package KayaVue
 */

if (!defined('ABSPATH')) {
    exit;
}

require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/queries.php';
require get_template_directory() . '/inc/diagrams.php';

add_action('after_setup_theme', 'kayavue_setup');
add_action('wp_enqueue_scripts', 'kayavue_assets');
add_action('login_enqueue_scripts', 'kayavue_login_assets');
add_filter('login_headerurl', static function () {
    return home_url('/');
});
add_filter('login_headertext', static function () {
    return 'KayaVue';
});
add_filter('document_title_parts', 'kayavue_document_title');
add_filter('body_class', static function ($classes) {
    $classes[] = 'kayavue';
    return $classes;
});

/**
 * Theme supports.
 */
function kayavue_setup() {
    load_theme_textdomain('kayavue', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('html5', ['search-form', 'comment-list', 'comment-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    register_nav_menus([
        'primary' => 'Primary',
        'footer' => 'Footer',
    ]);
}

/**
 * Front-end styles.
 */
function kayavue_assets() {
    $css = get_template_directory() . '/assets/css/main.css';
    wp_enqueue_style(
        'kayavue',
        get_template_directory_uri() . '/assets/css/main.css',
        [],
        file_exists($css) ? (string) filemtime($css) : wp_get_theme()->get('Version')
    );
}

/**
 * Login screen, so the demo admin door matches the public site.
 */
function kayavue_login_assets() {
    kayavue_assets();
    $favicon = get_template_directory_uri() . '/assets/favicon.svg';
    wp_add_inline_style('kayavue', '
        body.login { background: #F3EEE6; font-family: Outfit, "Noto Sans", sans-serif; }
        .login h1 a {
            background: none !important;
            text-indent: 0;
            width: auto;
            height: auto;
            font-family: Fraunces, Georgia, serif;
            font-weight: 560;
            font-size: 2rem;
            color: #1A1814;
            overflow: visible;
        }
        .login form { border: 1px solid #E0D5C6; border-radius: 16px; box-shadow: none; }
        .wp-core-ui .button-primary { background: #8E3B24; border-color: #732F1C; }
        .wp-core-ui .button-primary:hover { background: #732F1C; }
    ');
    add_action('login_head', static function () use ($favicon) {
        echo '<link rel="icon" href="' . esc_url($favicon) . '" type="image/svg+xml">';
    });
}

/**
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function kayavue_document_title($parts) {
    if (is_page('explore')) {
        $parts['title'] = kayavue_explore_heading();
    }
    return $parts;
}

/**
 * Heading for the filter page, shared by the title and the template.
 */
function kayavue_explore_heading() {
    $procedure = kayavue_find_published('kv_procedure', kayavue_request_slug('procedure'));
    $city_slug = kayavue_request_slug('city');
    $city_name = '';
    if ($city_slug !== '') {
        $term = get_term_by('slug', $city_slug, 'kv_city');
        if ($term && !is_wp_error($term)) {
            $city_name = $term->name;
        }
    }
    if ($procedure && $city_name) {
        return $procedure->post_title . ' in ' . $city_name;
    }
    if ($procedure) {
        return $procedure->post_title . ' across cities';
    }
    if ($city_name) {
        return 'Clinics and stories in ' . $city_name;
    }
    return 'Explore procedures and cities';
}

/**
 * Favicon on public pages.
 */
add_action('wp_head', static function () {
    echo '<link rel="icon" href="' . esc_url(get_template_directory_uri() . '/assets/favicon.svg') . '" type="image/svg+xml">' . "\n";
}, 2);
