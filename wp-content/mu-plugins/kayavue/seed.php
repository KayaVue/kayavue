<?php
/**
 * Idempotent demo seed. Runs once, and again with `wp kayavue seed --force`.
 *
 * @package KayaVue
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'kayavue_maybe_seed', 20);

if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('kayavue seed', function ($args, $assoc) {
        kayavue_run_seed(!empty($assoc['force']));
    });
}

/**
 * Seed from the first web request if the CLI installer has not run yet.
 */
function kayavue_maybe_seed() {
    if (defined('WP_CLI') && WP_CLI) {
        return;
    }
    if (defined('WP_INSTALLING') && WP_INSTALLING) {
        return;
    }
    if (!function_exists('is_blog_installed') || !is_blog_installed()) {
        return;
    }
    if (get_option('kayavue_seed_version') === KAYAVUE_SEED_VERSION) {
        return;
    }
    kayavue_run_seed(false);
}

/**
 * Create or update demo terms, posts, and pages.
 *
 * @param bool $force Rebuild even when this seed version is already recorded.
 */
function kayavue_run_seed($force = false) {
    if (!$force && get_option('kayavue_seed_version') === KAYAVUE_SEED_VERSION) {
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::log('KayaVue demo content is already seeded. Pass --force to rebuild it.');
        }
        return;
    }

    if (get_transient('kayavue_seed_lock')) {
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::warning('Another seed is already running.');
        }
        return;
    }

    set_transient('kayavue_seed_lock', 1, 3 * MINUTE_IN_SECONDS);

    $data = require __DIR__ . '/seed-data.php';

    kayavue_seed_delete_defaults();

    $cities = [];
    $order = 1;
    foreach ($data['cities'] as $slug => $city) {
        $term_id = kayavue_seed_term('kv_city', $slug, $city['name'], $city['description']);
        if ($term_id) {
            update_term_meta($term_id, 'kv_order', $order);
            $cities[$slug] = $term_id;
        }
        $order++;
    }

    foreach ($data['concerns'] as $slug => $concern) {
        kayavue_seed_term('kv_concern', $slug, $concern['name'], $concern['description']);
    }

    $procedures = [];
    foreach ($data['procedures'] as $procedure) {
        $id = kayavue_seed_upsert('kv_procedure', $procedure['slug'], [
            'post_title' => $procedure['title'],
            'post_excerpt' => $procedure['excerpt'],
            'post_content' => kayavue_seed_procedure_html($procedure),
            'menu_order' => (int) $procedure['order'],
        ], [
            'kv_cost_min' => (int) $procedure['cost_min'],
            'kv_cost_max' => (int) $procedure['cost_max'],
            'kv_cost_basis' => $procedure['cost_basis'],
            'kv_recovery' => $procedure['recovery'],
            'kv_downtime' => $procedure['downtime'],
            'kv_anesthesia' => $procedure['anesthesia'],
            'kv_sessions' => $procedure['sessions'],
            'kv_featured' => !empty($procedure['featured']) ? '1' : '0',
            'kv_diagram' => $procedure['diagram'],
        ], [
            'kv_concern' => $procedure['concern'],
        ]);
        if ($id) {
            $procedures[$procedure['slug']] = $id;
        }
    }

    $clinics = [];
    foreach ($data['clinics'] as $clinic) {
        $offer_ids = [];
        foreach ($clinic['offers'] as $offer_slug) {
            if (!empty($procedures[$offer_slug])) {
                $offer_ids[] = (string) $procedures[$offer_slug];
            }
        }
        $id = kayavue_seed_upsert('kv_clinic', $clinic['slug'], [
            'post_title' => $clinic['title'],
            'post_excerpt' => $clinic['excerpt'],
            'post_content' => kayavue_seed_paragraphs($clinic['paragraphs']),
        ], [
            'kv_area' => $clinic['area'],
            'kv_address' => $clinic['address'],
            'kv_established' => $clinic['established'],
            'kv_doctor' => $clinic['doctor'],
            'kv_qualifications' => $clinic['qualifications'],
            'kv_focus' => $clinic['focus'],
            'kv_offers' => $offer_ids,
        ], [
            'kv_city' => $clinic['city'],
        ]);
        if ($id) {
            $clinics[$clinic['slug']] = $id;
        }
    }

    $review_count = 0;
    foreach ($data['reviews'] as $review) {
        if (empty($procedures[$review['procedure']]) || empty($clinics[$review['clinic']])) {
            if (defined('WP_CLI') && WP_CLI) {
                WP_CLI::warning('Skipped review ' . $review['slug'] . ' because a link was missing.');
            }
            continue;
        }
        $id = kayavue_seed_upsert('kv_review', $review['slug'], [
            'post_title' => $review['title'],
            'post_excerpt' => wp_trim_words($review['story'], 28, '…'),
            'post_content' => '<p>' . esc_html($review['story']) . '</p>',
            'post_date' => $review['date'],
            'post_date_gmt' => get_gmt_from_date($review['date']),
        ], [
            'kv_procedure_id' => (string) $procedures[$review['procedure']],
            'kv_clinic_id' => (string) $clinics[$review['clinic']],
            'kv_rating' => (string) (int) $review['rating'],
            'kv_worth' => $review['worth'],
            'kv_reviewer' => $review['reviewer'],
            'kv_age_band' => $review['age'],
            'kv_when' => $review['when'],
        ], [
            'kv_city' => $review['city'],
        ]);
        if ($id) {
            $review_count++;
        }
    }

    foreach ($data['pages'] as $page) {
        $id = kayavue_seed_upsert('page', $page['slug'], [
            'post_title' => $page['title'],
            'post_excerpt' => $page['excerpt'],
            'post_content' => kayavue_seed_blocks($page['blocks']),
        ]);
        if ($id && !empty($page['template'])) {
            update_post_meta($id, '_wp_page_template', $page['template']);
        }
    }

    flush_rewrite_rules(false);
    update_option('kayavue_seed_version', KAYAVUE_SEED_VERSION);
    delete_transient('kayavue_seed_lock');

    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::success(sprintf(
            'Seeded %d procedures, %d clinics, %d reviews.',
            count($procedures),
            count($clinics),
            $review_count
        ));
    }
}

/**
 * Remove the stock sample post and page so the demo starts clean.
 */
function kayavue_seed_delete_defaults() {
    foreach (['post' => 'hello-world', 'page' => 'sample-page'] as $type => $slug) {
        $id = kayavue_seed_find($type, $slug);
        if ($id) {
            wp_delete_post($id, true);
        }
    }
}

/**
 * @param string $taxonomy Taxonomy name.
 * @param string $slug     Term slug.
 * @param string $name     Term name.
 * @param string $description Term description.
 */
function kayavue_seed_term($taxonomy, $slug, $name, $description) {
    $existing = term_exists($slug, $taxonomy);
    if ($existing) {
        $term_id = (int) (is_array($existing) ? $existing['term_id'] : $existing);
        wp_update_term($term_id, $taxonomy, [
            'name' => $name,
            'description' => $description,
            'slug' => $slug,
        ]);
        return $term_id;
    }
    $created = wp_insert_term($name, $taxonomy, [
        'slug' => $slug,
        'description' => $description,
    ]);
    if (is_wp_error($created)) {
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::warning($created->get_error_message());
        }
        return 0;
    }
    return (int) $created['term_id'];
}

/**
 * @param string               $type Post type.
 * @param string               $slug Post slug.
 * @param array<string, mixed> $postarr Post fields.
 * @param array<string, mixed> $meta Meta values. kv_offers may be a list.
 * @param array<string, string|array<int, string>> $tax Taxonomy => term slug or slugs.
 */
function kayavue_seed_upsert($type, $slug, $postarr, $meta = [], $tax = []) {
    $id = kayavue_seed_find($type, $slug);
    $postarr = array_merge([
        'post_type' => $type,
        'post_name' => $slug,
        'post_status' => 'publish',
        'post_author' => 1,
        'comment_status' => 'closed',
        'ping_status' => 'closed',
    ], $postarr);

    if ($id) {
        $postarr['ID'] = $id;
        $result = wp_update_post(wp_slash($postarr), true);
    } else {
        $result = wp_insert_post(wp_slash($postarr), true);
    }

    if (is_wp_error($result)) {
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::warning($slug . ': ' . $result->get_error_message());
        }
        return 0;
    }

    $id = (int) $result;
    foreach ($meta as $key => $value) {
        if ($key === 'kv_offers') {
            delete_post_meta($id, 'kv_offers');
            foreach ((array) $value as $offer) {
                add_post_meta($id, 'kv_offers', (string) $offer);
            }
            continue;
        }
        update_post_meta($id, $key, $value);
    }
    foreach ($tax as $taxonomy => $terms) {
        wp_set_object_terms($id, $terms, $taxonomy, false);
    }
    return $id;
}

/**
 * @param string $type Post type.
 * @param string $slug Post name.
 */
function kayavue_seed_find($type, $slug) {
    $ids = get_posts([
        'name' => $slug,
        'post_type' => $type,
        'post_status' => 'any',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'suppress_filters' => true,
    ]);
    return $ids ? (int) $ids[0] : 0;
}

/**
 * @param array<int, string> $paragraphs Plain paragraphs.
 */
function kayavue_seed_paragraphs($paragraphs) {
    $html = '';
    foreach ($paragraphs as $paragraph) {
        $html .= '<p>' . esc_html($paragraph) . '</p>';
    }
    return $html;
}

/**
 * @param array<string, mixed> $procedure Procedure seed row.
 */
function kayavue_seed_procedure_html($procedure) {
    $html = '';
    foreach ($procedure['sections'] as $heading => $paragraphs) {
        $html .= '<h2>' . esc_html($heading) . '</h2>';
        $html .= kayavue_seed_paragraphs($paragraphs);
    }
    if (!empty($procedure['questions'])) {
        $html .= '<h2>Questions worth asking</h2><ul>';
        foreach ($procedure['questions'] as $question) {
            $html .= '<li>' . esc_html($question) . '</li>';
        }
        $html .= '</ul>';
    }
    return $html;
}

/**
 * @param array<int, array{0: string, 1: mixed}> $blocks Tag plus text or list items.
 */
function kayavue_seed_blocks($blocks) {
    $html = '';
    foreach ($blocks as $block) {
        $tag = $block[0];
        $body = $block[1];
        if ($tag === 'ul' || $tag === 'ol') {
            $html .= '<' . $tag . '>';
            foreach ((array) $body as $item) {
                $html .= '<li>' . esc_html($item) . '</li>';
            }
            $html .= '</' . $tag . '>';
            continue;
        }
        $html .= '<' . $tag . '>' . esc_html((string) $body) . '</' . $tag . '>';
    }
    return $html;
}
