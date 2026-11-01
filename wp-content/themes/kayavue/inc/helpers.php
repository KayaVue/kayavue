<?php
/**
 * Presentation helpers.
 *
 * @package KayaVue
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Indian digit grouping, with a rupee sign.
 *
 * @param int|string $amount Amount in rupees.
 */
function kayavue_inr($amount) {
    $amount = (int) $amount;
    $digits = (string) abs($amount);
    $sign = $amount < 0 ? '-' : '';
    if (strlen($digits) <= 3) {
        return $sign . '₹' . $digits;
    }
    $last = substr($digits, -3);
    $rest = substr($digits, 0, -3);
    $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
    return $sign . '₹' . $rest . ',' . $last;
}

/**
 * @param int|string $min Minimum rupees.
 * @param int|string $max Maximum rupees.
 */
function kayavue_inr_range($min, $max) {
    return kayavue_inr($min) . '–' . kayavue_inr($max);
}

/**
 * @param string $key Query-string key.
 */
function kayavue_request_slug($key) {
    if (!isset($_GET[$key])) {
        return '';
    }
    return sanitize_title(wp_unslash($_GET[$key]));
}

/**
 * @param string $type Post type.
 * @param string $slug Post name.
 * @return WP_Post|null
 */
function kayavue_find_published($type, $slug) {
    if ($slug === '') {
        return null;
    }
    $posts = get_posts([
        'name' => $slug,
        'post_type' => $type,
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'suppress_filters' => true,
    ]);
    return $posts ? $posts[0] : null;
}

/**
 * @return WP_Post[]
 */
function kayavue_all_procedures() {
    static $posts = null;
    if ($posts === null) {
        $posts = get_posts([
            'post_type' => 'kv_procedure',
            'post_status' => 'publish',
            'posts_per_page' => 100,
            'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
        ]);
    }
    return $posts;
}

/**
 * @return WP_Term[]
 */
function kayavue_all_cities() {
    static $terms = null;
    if ($terms === null) {
        $terms = get_terms([
            'taxonomy' => 'kv_city',
            'hide_empty' => false,
            'meta_key' => 'kv_order',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
        ]);
        if (is_wp_error($terms)) {
            $terms = [];
        }
    }
    return $terms;
}

/**
 * @param int $post_id Post ID.
 * @return WP_Term|null
 */
function kayavue_primary_term($post_id, $taxonomy) {
    $terms = get_the_terms($post_id, $taxonomy);
    if (!$terms || is_wp_error($terms)) {
        return null;
    }
    return $terms[0];
}

/**
 * @param int $post_id Post ID.
 */
function kayavue_tone_class($post_id) {
    $term = kayavue_primary_term($post_id, 'kv_concern');
    $map = [
        'hair' => 'tone-hair',
        'face' => 'tone-face',
        'eyes' => 'tone-eyes',
        'body' => 'tone-body',
        'breast' => 'tone-breast',
        'skin' => 'tone-skin',
        'dental' => 'tone-dental',
    ];
    $slug = $term ? $term->slug : 'face';
    return $map[$slug] ?? 'tone-face';
}

/**
 * @return WP_Post[]
 */
function kayavue_reviews($filters = []) {
    static $all = null;
    if ($all === null) {
        $all = get_posts([
            'post_type' => 'kv_review',
            'post_status' => 'publish',
            'posts_per_page' => 200,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
        foreach ($all as $review) {
            $review->kv_rating = (int) get_post_meta($review->ID, 'kv_rating', true);
            $review->kv_worth = (string) get_post_meta($review->ID, 'kv_worth', true);
            $review->kv_procedure_id = (int) get_post_meta($review->ID, 'kv_procedure_id', true);
            $review->kv_clinic_id = (int) get_post_meta($review->ID, 'kv_clinic_id', true);
            $review->kv_reviewer = (string) get_post_meta($review->ID, 'kv_reviewer', true);
            $review->kv_age_band = (string) get_post_meta($review->ID, 'kv_age_band', true);
            $review->kv_when = (string) get_post_meta($review->ID, 'kv_when', true);
        }
    }

    $out = $all;
    if (!empty($filters['procedure_id'])) {
        $pid = (int) $filters['procedure_id'];
        $out = array_values(array_filter($out, static function ($review) use ($pid) {
            return (int) $review->kv_procedure_id === $pid;
        }));
    }
    if (!empty($filters['clinic_id'])) {
        $cid = (int) $filters['clinic_id'];
        $out = array_values(array_filter($out, static function ($review) use ($cid) {
            return (int) $review->kv_clinic_id === $cid;
        }));
    }
    if (!empty($filters['city'])) {
        $slug = $filters['city'];
        $out = array_values(array_filter($out, static function ($review) use ($slug) {
            return has_term($slug, 'kv_city', $review);
        }));
    }
    if (!empty($filters['limit'])) {
        $out = array_slice($out, 0, (int) $filters['limit']);
    }
    return $out;
}

/**
 * @param WP_Post[] $reviews Reviews.
 * @return array{count:int,avg:float|null,worth_pct:int|null,yes:int,no:int,unsure:int}
 */
function kayavue_stats($reviews) {
    $count = count($reviews);
    if ($count === 0) {
        return [
            'count' => 0,
            'avg' => null,
            'worth_pct' => null,
            'yes' => 0,
            'no' => 0,
            'unsure' => 0,
        ];
    }
    $sum = 0;
    $yes = 0;
    $no = 0;
    $unsure = 0;
    foreach ($reviews as $review) {
        $sum += (int) $review->kv_rating;
        if ($review->kv_worth === 'yes') {
            $yes++;
        } elseif ($review->kv_worth === 'no') {
            $no++;
        } else {
            $unsure++;
        }
    }
    return [
        'count' => $count,
        'avg' => round($sum / $count, 1),
        'worth_pct' => (int) round(($yes / $count) * 100),
        'yes' => $yes,
        'no' => $no,
        'unsure' => $unsure,
    ];
}

/**
 * @param array{count:int,worth_pct:int|null} $stats Stats.
 */
function kayavue_worth_sentence($stats) {
    if (empty($stats['count'])) {
        return 'No demo stories are filed here yet.';
    }
    $noun = ((int) $stats['count'] === 1) ? 'demo story' : 'demo stories';
    return sprintf(
        '%d%% of %d %s said it was worth it for them. That is a self-reported share, not a clinical success rate.',
        (int) $stats['worth_pct'],
        (int) $stats['count'],
        $noun
    );
}

/**
 * @param string $worth yes|no|unsure.
 */
function kayavue_worth_label($worth) {
    if ($worth === 'yes') {
        return 'Said it was worth it for them';
    }
    if ($worth === 'no') {
        return 'Said it was not worth it for them';
    }
    return 'Said they are unsure it was worth it';
}

/**
 * @param array<string, mixed> $filters Procedure and city filters.
 * @return WP_Post[]
 */
function kayavue_clinics($filters = []) {
    $query = [
        'post_type' => 'kv_clinic',
        'post_status' => 'publish',
        'posts_per_page' => 100,
        'orderby' => 'title',
        'order' => 'ASC',
    ];
    $meta = [];
    $tax = [];
    if (!empty($filters['procedure_id'])) {
        $meta[] = [
            'key' => 'kv_offers',
            'value' => (string) (int) $filters['procedure_id'],
        ];
    }
    if (!empty($filters['city'])) {
        $tax[] = [
            'taxonomy' => 'kv_city',
            'field' => 'slug',
            'terms' => $filters['city'],
        ];
    }
    if ($meta) {
        $query['meta_query'] = $meta;
    }
    if ($tax) {
        $query['tax_query'] = $tax;
    }
    return get_posts($query);
}

/**
 * @param int $clinic_id Clinic ID.
 * @return WP_Post[]
 */
function kayavue_clinic_procedures($clinic_id) {
    $ids = array_filter(array_map('intval', (array) get_post_meta($clinic_id, 'kv_offers', false)));
    if (!$ids) {
        return [];
    }
    return get_posts([
        'post_type' => 'kv_procedure',
        'post_status' => 'publish',
        'post__in' => $ids,
        'posts_per_page' => 20,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]);
}

/**
 * Clinics with at least two stories, highest average first.
 *
 * @param int $limit How many.
 * @return WP_Post[]
 */
function kayavue_top_clinics($limit = 4) {
    $clinics = kayavue_clinics();
    usort($clinics, static function ($a, $b) {
        $sa = kayavue_stats(kayavue_reviews(['clinic_id' => $a->ID]));
        $sb = kayavue_stats(kayavue_reviews(['clinic_id' => $b->ID]));
        $avga = $sa['avg'] ?? -1;
        $avgb = $sb['avg'] ?? -1;
        if ($avga !== $avgb) {
            return $avgb <=> $avga;
        }
        return $sb['count'] <=> $sa['count'];
    });
    $clinics = array_values(array_filter($clinics, static function ($clinic) {
        return kayavue_stats(kayavue_reviews(['clinic_id' => $clinic->ID]))['count'] >= 2;
    }));
    return array_slice($clinics, 0, $limit);
}

/**
 * @param WP_Post $procedure Procedure.
 * @param int     $limit     How many.
 * @return WP_Post[]
 */
function kayavue_related_procedures($procedure, $limit = 3) {
    $term = kayavue_primary_term($procedure->ID, 'kv_concern');
    $related = [];
    foreach (kayavue_all_procedures() as $candidate) {
        if ((int) $candidate->ID === (int) $procedure->ID) {
            continue;
        }
        if ($term && has_term($term->slug, 'kv_concern', $candidate)) {
            $related[] = $candidate;
        }
    }
    if (count($related) < $limit) {
        foreach (kayavue_all_procedures() as $candidate) {
            if ((int) $candidate->ID === (int) $procedure->ID) {
                continue;
            }
            if (!in_array($candidate, $related, true)) {
                $related[] = $candidate;
            }
            if (count($related) >= $limit) {
                break;
            }
        }
    }
    return array_slice($related, 0, $limit);
}

/**
 * @return array<int, array{key:string,label:string,url:string}>
 */
function kayavue_nav_items() {
    return [
        [
            'key' => 'procedures',
            'label' => 'Procedures',
            'url' => get_post_type_archive_link('kv_procedure') ?: home_url('/procedures/'),
        ],
        [
            'key' => 'clinics',
            'label' => 'Clinics',
            'url' => get_post_type_archive_link('kv_clinic') ?: home_url('/clinics/'),
        ],
        [
            'key' => 'reviews',
            'label' => 'Reviews',
            'url' => get_post_type_archive_link('kv_review') ?: home_url('/reviews/'),
        ],
        [
            'key' => 'cities',
            'label' => 'Cities',
            'url' => home_url('/cities/'),
        ],
        [
            'key' => 'how',
            'label' => 'How it works',
            'url' => home_url('/how-it-works/'),
        ],
    ];
}

/**
 * @param string $key Nav key.
 */
function kayavue_nav_is_current($key) {
    switch ($key) {
        case 'procedures':
            return is_post_type_archive('kv_procedure') || is_singular('kv_procedure') || is_tax('kv_concern');
        case 'clinics':
            return is_post_type_archive('kv_clinic') || is_singular('kv_clinic');
        case 'reviews':
            return is_post_type_archive('kv_review') || is_singular('kv_review');
        case 'cities':
            return is_page('cities') || is_tax('kv_city');
        case 'how':
            return is_page('how-it-works');
        default:
            return false;
    }
}

/**
 * @param string $class List class.
 */
function kayavue_primary_nav($class) {
    echo '<ul class="' . esc_attr($class) . '">';
    foreach (kayavue_nav_items() as $item) {
        $current = kayavue_nav_is_current($item['key']);
        echo '<li><a href="' . esc_url($item['url']) . '"' . ($current ? ' aria-current="page"' : '') . '>' . esc_html($item['label']) . '</a></li>';
    }
    echo '</ul>';
}

/**
 * @param float|int|null $avg Average rating.
 */
function kayavue_stars_markup($avg) {
    $value = $avg === null ? 0 : (float) $avg;
    $rounded = (int) round($value);
    $label = $avg === null ? 'No rating yet' : sprintf('%s out of 5', number_format($value, 1));
    $html = '<span class="stars" role="img" aria-label="' . esc_attr($label) . '">';
    for ($i = 1; $i <= 5; $i++) {
        $filled = $i <= $rounded;
        $html .= '<svg viewBox="0 0 20 20" aria-hidden="true" class="' . ($filled ? 'is-on' : 'is-off') . '"><path d="M10 1.8 12.4 7l5.6.6-4.2 3.8 1.2 5.5L10 14.2 5 16.9l1.2-5.5L2 7.6 7.6 7z"/></svg>';
    }
    $html .= '</span>';
    return $html;
}

/**
 * @param string               $title Heading.
 * @param string               $body  Supporting line.
 * @param array<int, array{0:string,1:string}> $links Label, URL.
 */
function kayavue_empty_state($title, $body, $links = []) {
    echo '<div class="empty">';
    echo '<p class="kicker">Nothing in this filter</p>';
    echo '<h2>' . esc_html($title) . '</h2>';
    echo '<p>' . esc_html($body) . '</p>';
    if ($links) {
        echo '<div class="empty-links">';
        foreach ($links as $link) {
            echo '<a class="btn btn-ghost" href="' . esc_url($link[1]) . '">' . esc_html($link[0]) . '</a>';
        }
        echo '</div>';
    }
    echo '</div>';
}
