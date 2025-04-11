<?php

namespace SixamTech\Admin;

class Contacts
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_contacts_api_route'));
        add_action('admin_enqueue_scripts', array($this, 'load_contact_script'));
        add_action('admin_menu', array($this, 'register_contacts_menu'));
        // add_action('admin_post_sixamtech_add_contact', [$this, 'handle_add_contact']);
        // add_action('admin_post_sixamtech_delete_contact', [$this, 'handle_delete_contact']);
    }

    public function load_contact_script()
    {
        $screen = get_current_screen();
        if ($screen->id !== '6amtech-task_page_sixamtech-add-contact') {
            return;
        }
        wp_enqueue_script("contact_script", plugins_url('/6amtech-task/assets/js/scripts.js'), array('jquery'), false, true);
        wp_localize_script('contact_script', 'API_DATA', [
            'nonce' => wp_create_nonce('wp_rest'),
            'url'   => rest_url('6amTech/v1/contacts/')
        ]);

        wp_enqueue_style('toastr-css', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css');
        wp_enqueue_script('toastr-js', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js', array('jquery'), null, true);
    }

    public function register_contacts_api_route()
    {
        register_rest_route('6amTech/v1', '/contacts/', array(
            'methods' => "POST",
            "callback" => array($this, "handle_contact_requests"),
            'permission_callback' => array($this, 'check_authentication'),
        ));
    }

    public function check_authentication()
    {
        return is_user_logged_in();
    }

    public function handle_contact_requests($request)
    {
        global $wpdb;

        $params = $request->get_json_params();

        $name    = sanitize_text_field($params['name']);
        $email   = sanitize_email($params['email']);
        $mobile  = sanitize_text_field($params['mobile']);
        $address = sanitize_textarea_field($params['address']);

        if (!$name || !$email || !$mobile || !$address) {
            return new \WP_REST_Response([
                'message' => 'All fields are required.',
            ], 400);
        }

        $wpdb->insert('contact_list', [
            'name'    => $name,
            'email'   => $email,
            'mobile'  => $mobile,
            'address' => $address,
        ]);

        if ($wpdb->insert_id) {
            return new \WP_REST_Response([
                'message' => "Contact added successfully!",
                'id'      => $wpdb->insert_id
            ], 200);
        } else {
            return new \WP_REST_Response([
                'message' => "Something went wrong while saving the contact.",
            ], 500);
        }
    }

    public function register_contacts_menu()
    {
        add_menu_page(
            '6amTech - Task',
            '6amTech - Task',
            'manage_options',
            'sixamtech-contacts',
            [$this, 'contacts_page'],
            'dashicons-list-view'
        );

        add_submenu_page(
            'sixamtech-contacts',
            'Contact list',
            'Contact list',
            'manage_options',
            'sixamtech-contacts',
            [$this, 'contacts_page'],
        );

        add_submenu_page(
            'sixamtech-contacts',
            'Add New Contact',
            'Add New Contact',
            'manage_options',
            'sixamtech-add-contact',
            [$this, 'add_new_contact_page']
        );
    }

    public function contacts_page()
    {
        echo '<div class="wrap"><h1>Contact List</h1>';
        // $this->render_contacts_list();
        echo '</div>';
    }

    public function add_new_contact_page()
    {
        echo '<div class="wrap"><h1>Add New Contact</h1>';
        $this->render_add_form();
        echo '</div>';
    }

    // private function render_contacts_list()
    // {
    //     global $wpdb;
    //     $table = $wpdb->prefix . 'contact_list';

    //     $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    //     $limit = 5;
    //     $offset = ($paged - 1) * $limit;

    //     $contacts = $wpdb->get_results("SELECT * FROM $table LIMIT $limit OFFSET $offset");
    //     $total = $wpdb->get_var("SELECT COUNT(*) FROM $table");
    //     $total_pages = ceil($total / $limit);

    //     echo '<table class="widefat"><thead><tr><th>Name</th><th>Email</th><th>Mobile</th><th>Address</th><th>Actions</th></tr></thead><tbody>';
    //     foreach ($contacts as $contact) {
    //         $delete_url = wp_nonce_url(admin_url('admin-post.php?action=sixamtech_delete_contact&id=' . $contact->id), 'sixamtech_delete_contact');
    //         echo '<tr>';
    //         echo '<td>' . esc_html($contact->name) . '</td>';
    //         echo '<td>' . esc_html($contact->email) . '</td>';
    //         echo '<td>' . esc_html($contact->mobile) . '</td>';
    //         echo '<td>' . esc_html($contact->address) . '</td>';
    //         echo '<td><a href="' . $delete_url . '" onclick="return confirm(\'Are you sure?\')">Delete</a></td>';
    //         echo '</tr>';
    //     }
    //     echo '</tbody></table>';

    //     if ($total_pages > 1) {
    //         echo '<div class="tablenav"><div class="tablenav-pages">';
    //         for ($i = 1; $i <= $total_pages; $i++) {
    //             $class = ($i == $paged) ? 'class="current"' : '';
    //             echo "<a $class href='?page=sixamtech-contacts&paged=$i'>$i</a> ";
    //         }
    //         echo '</div></div>';
    //     }
    // }

    private function render_add_form()
    {
?>
<form id="sixamtech-contact-form">
    <table class="form-table">
        <tr>
            <th>Name</th>
            <td><input type="text" name="name" required class="regular-text"
                    placeholder="<?php _e("e.g: Al Mahmud Alif", "sixAmTech") ?>" /></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><input type="email" name="email" required class="regular-text"
                    placeholder="<?php _e("e.g: abc@example.com", "sixAmTech") ?>" /></td>
        </tr>
        <tr>
            <th>Mobile</th>
            <td><input type="tel" pattern="^01[3-9][0-9]{8}$" name="mobile" required class="regular-text"
                    placeholder="<?php _e("e.g: 01791322247", "sixAmTech") ?>" /></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><textarea name="address" required class="large-text"
                    placeholder="<?php _e("e.g: West kawnia, Barishal 8200", "sixAmTech") ?>"></textarea></td>
        </tr>
    </table>
    <button type="submit" class="button button-primary"><?php _e("Add contact", "sixAmTech") ?></button>
</form>
<?php
    }

    // public function handle_add_contact()
    // {
    //     if (!current_user_can('manage_options') || !check_admin_referer('sixamtech_add_contact')) {
    //         wp_die('Permission denied');
    //     }

    //     global $wpdb;
    //     $table = $wpdb->prefix . 'contact_list';
    //     $wpdb->insert($table, [
    //         'name' => sanitize_text_field($_POST['name']),
    //         'email' => sanitize_email($_POST['email']),
    //         'mobile' => sanitize_text_field($_POST['mobile']),
    //         'address' => sanitize_textarea_field($_POST['address']),
    //     ]);

    //     wp_redirect(admin_url('admin.php?page=sixamtech-contacts'));
    //     exit;
    // }

    // public function handle_delete_contact()
    // {
    //     if (!current_user_can('manage_options') || !check_admin_referer('sixamtech_delete_contact')) {
    //         wp_die('Permission denied');
    //     }

    //     global $wpdb;
    //     $table = $wpdb->prefix . 'contact_list';
    //     $wpdb->delete($table, ['id' => intval($_GET['id'])]);

    //     wp_redirect(admin_url('admin.php?page=sixamtech-contacts'));
    //     exit;
    // }
}