<?php
// This file is automatically triggered when the plugin is uninstalled

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die; // Don't allow direct access to this file
}

global $wpdb;

// Define the custom table name (ensure you use the correct table prefix)
$table_name = 'contact_list';

// Delete the table
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);