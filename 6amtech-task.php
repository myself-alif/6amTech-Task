<?php

/**
 * Plugin Name: 6amTech - Task
 * Description: A custom task plugin for managing contacts.
 * Version: 1.0
 * Author: Al Mahmud Alif
 * Text domain: sixAmTech
 */

if (! defined('ABSPATH')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Activation Hook
register_activation_hook(__FILE__, ['SixamTech\Activator', 'activate']);
add_action("wp_enqueue_scripts", "load_frontend_css");
function load_frontend_css()
{
    wp_enqueue_style("frontend_css", plugin_dir_url(__FILE__) . "/assets/css/frontend.css", null, false);
}

new SixamTech\Admin\Settings();
new SixamTech\Frontend\WelcomeMessage();
new SixamTech\Api\CreateAPI();
new SixamTech\Admin\Contacts();
new \SixamTech\Frontend\Shortcode();
