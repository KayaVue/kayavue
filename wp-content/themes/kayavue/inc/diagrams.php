<?php
/**
 * Original line diagrams. Not photographs and not predictions.
 *
 * @package KayaVue
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @param string $key   Diagram key.
 * @param string $phase before|after.
 */
function kayavue_diagram($key, $phase) {
    $phase = $phase === 'before' ? 'before' : 'after';
    $label = $phase === 'before' ? 'Before' : 'After';
    $title = ucfirst(str_replace('-', ' ', $key)) . ', ' . strtolower($label) . ' — diagram, not a photograph';
    $svg = '<svg viewBox="0 0 220 150" role="img" aria-label="' . esc_attr($title) . '">';
    $svg .= '<title>' . esc_html($title) . '</title>';
    $svg .= '<rect width="220" height="150" rx="14" fill="#F7F1E8"/>';
    $svg .= '<rect x="14" y="14" width="192" height="122" rx="10" fill="none" stroke="#E4D9C8" stroke-width="1"/>';
    $svg .= kayavue_diagram_paths($key, $phase);
    $svg .= '</svg>';
    return '<figure class="diagram diagram-' . esc_attr($phase) . '">' . $svg
        . '<figcaption><strong>' . esc_html($label) . '</strong> <span>Diagram, not a photograph</span></figcaption></figure>';
}

/**
 * @param string $key   Diagram key.
 * @param string $phase before|after.
 */
function kayavue_diagram_paths($key, $phase) {
    $stroke = 'fill="none" stroke="#1A1814" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"';
    $soft = 'fill="none" stroke="#8E3B24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"';
    $wash = '<ellipse cx="110" cy="82" rx="46" ry="40" fill="#EFE4D4"/>';

    if ($key === 'nose' || $key === 'chin') {
        if ($phase === 'before' && $key === 'nose') {
            $profile = 'M78 36 C80 52 92 54 98 66 C118 70 120 84 96 90 C88 102 100 108 104 118 C112 132 92 138 74 134';
        } elseif ($phase === 'after' && $key === 'nose') {
            $profile = 'M78 36 C80 54 90 60 96 70 C104 78 102 86 94 90 C88 102 100 108 104 118 C112 132 92 138 74 134';
        } elseif ($phase === 'before') {
            $profile = 'M78 34 C82 58 96 62 100 78 C102 92 108 104 112 122 C100 140 78 138 70 128';
        } else {
            $profile = 'M78 34 C84 56 98 64 104 78 C108 90 112 102 108 120 C96 136 76 132 70 120';
        }
        return $wash . '<path ' . $stroke . ' d="' . $profile . '"/>'
            . '<path ' . $soft . ' d="' . ($key === 'nose'
                ? ($phase === 'before' ? 'M98 66 C112 69 116 80 100 86' : 'M96 70 C102 74 104 82 96 86')
                : ($phase === 'before' ? 'M104 112 C112 118 114 128 104 132' : 'M106 108 C116 114 114 126 102 128'))
            . '"/>';
    }

    if ($key === 'eye') {
        $lid = $phase === 'before'
            ? 'M58 78 Q110 58 162 78'
            : 'M58 74 Q110 50 162 74';
        $crease = $phase === 'before'
            ? 'M70 70 Q110 60 150 70'
            : 'M68 62 Q110 46 154 62';
        return '<path ' . $stroke . ' d="' . $lid . '"/>'
            . '<path ' . $soft . ' d="' . $crease . '"/>'
            . '<path ' . $stroke . ' d="M58 78 Q110 102 162 78"/>'
            . '<circle cx="110" cy="80" r="8" fill="#16352F"/>'
            . '<circle cx="113" cy="77" r="2" fill="#F7F1E8"/>';
    }

    if ($key === 'body' || $key === 'breast' || $key === 'chest') {
        if ($key === 'breast') {
            $curve = $phase === 'before'
                ? 'M78 40 V70 C78 92 96 100 110 100 C124 100 142 92 142 70 V40'
                : 'M78 40 V64 C78 96 94 112 110 112 C126 112 142 96 142 64 V40';
        } elseif ($key === 'chest') {
            $curve = $phase === 'before'
                ? 'M70 42 H150 V78 C150 100 132 112 110 112 C88 112 70 100 70 78 Z'
                : 'M70 42 H150 V86 C150 98 130 104 110 104 C90 104 70 98 70 86 Z';
        } else {
            $curve = $phase === 'before'
                ? 'M86 36 C84 70 70 90 72 118 C96 132 140 128 148 112 C156 86 132 72 128 36'
                : 'M90 36 C92 68 86 86 88 118 C104 128 136 126 140 112 C146 88 132 70 130 36';
        }
        return '<path ' . $stroke . ' d="' . $curve . '"/>'
            . '<path ' . $soft . ' d="' . ($phase === 'before' ? 'M96 78 H124' : 'M98 76 H122') . '"/>';
    }

    if ($key === 'dental') {
        $heights = $phase === 'before' ? [46, 62, 40, 58, 50] : [52, 52, 52, 52, 52];
        $svg = '';
        $x = 48;
        foreach ($heights as $height) {
            $y = 96 - $height;
            $svg .= '<rect x="' . $x . '" y="' . $y . '" width="20" height="' . $height . '" rx="4" fill="#FBF8F3" stroke="#1A1814" stroke-width="1.6"/>';
            $x += 26;
        }
        return $svg . '<path ' . $soft . ' d="M40 100 H182"/>';
    }

    if ($key === 'skin') {
        $dots = $phase === 'before'
            ? [[70, 50], [92, 64], [120, 48], [146, 70], [84, 88], [130, 96], [108, 74], [154, 90]]
            : [[96, 70], [124, 80], [110, 96]];
        $svg = '<ellipse cx="110" cy="78" rx="48" ry="40" fill="#F3E6D6" stroke="#1A1814" stroke-width="1.6"/>';
        foreach ($dots as $dot) {
            $svg .= '<circle cx="' . $dot[0] . '" cy="' . $dot[1] . '" r="2.2" fill="#8E3B24"/>';
        }
        return $svg;
    }

    $crown = $phase === 'before'
        ? 'M72 70 Q110 38 148 70'
        : 'M68 84 Q110 46 152 84';
    $strands = $phase === 'before'
        ? '<path ' . $soft . ' d="M90 52 Q96 40 108 46 M120 44 Q130 36 136 48"/>'
        : '<path ' . $soft . ' d="M78 62 Q86 44 102 52 M112 40 Q124 30 138 48 M96 48 Q104 34 118 42"/>';
    return '<ellipse cx="110" cy="92" rx="38" ry="36" fill="#EFE4D4" stroke="#1A1814" stroke-width="1.6"/>'
        . '<path ' . $stroke . ' d="' . $crown . '"/>'
        . $strands
        . '<path ' . $stroke . ' d="M86 96 Q110 108 134 96"/>';
}
