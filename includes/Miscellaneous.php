<?php

namespace SixamTech;

class Miscellaneous
{
    public static function create_table()
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

    public function delete_table()
    {
        global $wpdb;

        // Table name (use WordPress table prefix)
        $table_name =  'contact_list';

        // Delete the custom table
        $sql = "DROP TABLE IF EXISTS $table_name";

        // Run the query to delete the table
        $wpdb->query($sql);
    }
}
