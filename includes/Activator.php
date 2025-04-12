<?php

namespace SixamTech;

class Activator
{
    public static function activate()
    {
        // add_option('sixamtech_task_welcome_message', 'Welcome to our site!');
        global $wpdb;
        $table = 'contact_list';
        $charset_collate = $wpdb->get_charset_collate();
        //Create table
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $sql = "CREATE TABLE $table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            mobile varchar(20) NOT NULL,
            address text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);
    }
}