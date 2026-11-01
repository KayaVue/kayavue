<?php
/**
 * Plugin Name: KayaVue Core
 * Description: Procedures, clinics, reviews, cities, and a one-time demo seed for the KayaVue theme.
 * Author: KayaVue
 * Version: 1.0.0
 *
 * @package KayaVue
 */

if (!defined('ABSPATH')) {
    exit;
}

define('KAYAVUE_SEED_VERSION', '1');

require __DIR__ . '/kayavue/post-types.php';
require __DIR__ . '/kayavue/seed.php';
