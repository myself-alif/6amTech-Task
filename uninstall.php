<?php


if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

global $wpdb;
$table_name = 'contact_list';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);