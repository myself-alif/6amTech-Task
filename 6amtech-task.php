<?php

/**
 * Plugin Name: 6amTech - Task
 * Description: A custom task plugin for managing contacts.
 * Version: 1.0
 * Author: 6amTech
 * Text domain: sixAmTech
 */


if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Activation Hook
register_activation_hook(__FILE__, ['SixamTech\Activator', 'activate']);

new SixamTech\Admin\Settings();
new SixamTech\Frontend\WelcomeMessage();