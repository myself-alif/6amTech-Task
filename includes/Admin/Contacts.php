<?php

namespace SixamTech\Admin;

class Contacts
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'load_contact_script'));
        add_action('admin_menu', array($this, 'register_contacts_menu'));
    }

    public function load_contact_script()
    {
        $screen = get_current_screen();

        if ($screen->id === '6amtech-task_page_sixamtech-add-contact' or $screen->id === 'toplevel_page_sixamtech-contacts') {
            wp_enqueue_script("contact_script", plugins_url('/6amtech-task/assets/js/scripts.js'), array('jquery'), false, true);
            wp_localize_script('contact_script', 'API_DATA', array(
                'nonce' => wp_create_nonce('wp_rest'),
                'url'   => rest_url('6amTech/v1/contacts/')
            ));

            wp_enqueue_style('toastr-css', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css');
            wp_enqueue_script('toastr-js', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js', array('jquery'), null, true);
        } else {
            return;
        }
    }

    public function register_contacts_menu()
    {
        add_menu_page(
            '6amTech - Task',
            '6amTech - Task',
            'manage_options',
            'sixamtech-contacts',
            array($this, 'contacts_page'),
            'dashicons-list-view'
        );

        add_submenu_page(
            'sixamtech-contacts',
            'Contact list',
            'Contact list',
            'manage_options',
            'sixamtech-contacts',
            array($this, 'contacts_page'),
        );

        add_submenu_page(
            'sixamtech-contacts',
            'Add New Contact',
            'Add New Contact',
            'manage_options',
            'sixamtech-add-contact',
            array($this, 'add_new_contact_page')
        );
    }

    public function contacts_page()
    {
        $this->render_contacts_list();
        echo '</div>';
    }

    public function add_new_contact_page()
    {
        echo '<div class="wrap"><h1>Add New Contact</h1>';
        $this->render_add_form();
        echo '</div>';
    }

    private function render_contacts_list()
    {
        global $wpdb;

        $table_name = 'contact_list';
        $per_page = 5;

        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;


        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

        $contacts = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_name ORDER BY id ASC LIMIT %d OFFSET %d", $per_page, $offset)
        );


        echo '<div class="wrap"><h1>Contact List</h1>';
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th>Name</th><th>Email</th><th>Mobile</th><th>Address</th><th>Actions</th></tr></thead>';
        echo '<tbody>';

        if (!empty($contacts)) {
            foreach ($contacts as $contact) {
                echo '<tr>';
                echo '<td class="editable id" style="display:none">' . esc_html($contact->id) . '</td>';
                echo '<td class="editable name">' . esc_html($contact->name) . '</td>';
                echo '<td class="editable email">' . esc_html($contact->email) . '</td>';
                echo '<td class="editable mobile">' . esc_html($contact->mobile) . '</td>';
                echo '<td class="editable address">' . esc_html($contact->address) . '</td>';
                echo '<td>
                  <a href="#" class="button button-small edit-contact" data-id="' . esc_attr($contact->id) . '">';
                _e('Edit', 'sixAmTech');
                echo '</a>
                  <a href="#" class="button button-small data-update" style="display:none" data-id="' . esc_attr($contact->id) . '">';
                _e('Update', 'sixAmTech');
                echo '</a>
                <a href="#" class="button button-small button-danger delete-contact" data-id="' . esc_attr($contact->id) . '">';
                _e('Delete', 'sixAmTech');
                echo '</a>
                    </td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5">No contacts found.</td></tr>';
        }

        echo '</tbody></table>';

        $total_pages = ceil($total / $per_page);
        if ($total_pages > 1) {
            echo '<div class="tablenav"><div class="tablenav-pages">';
            echo paginate_links([
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => __('« Prev'),
                'next_text' => __('Next »'),
                'total' => $total_pages,
                'current' => $current_page
            ]);
            echo '</div></div>';
        }
        echo '</div>';
    }

    private function render_add_form()
    {
?>
<form id="sixamtech-contact-form">
    <table class="form-table">
        <tr>
            <th><?php _e("Name", "sixAmTech") ?></th>
            <td><input type="text" name="name" required class="regular-text"
                    placeholder="<?php _e("e.g: Al Mahmud Alif", "sixAmTech") ?>" /></td>
        </tr>
        <tr>
            <th><?php _e("Email", "sixAmTech") ?></th>
            <td><input type="email" name="email" required class="regular-text"
                    placeholder="<?php _e("e.g: abc@example.com", "sixAmTech") ?>" /></td>
        </tr>
        <tr>
            <th><?php _e("Mobile", "sixAmTech") ?></th>
            <td><input type="tel" pattern="^01[3-9][0-9]{8}$" name="mobile" required class="regular-text"
                    placeholder="<?php _e("e.g: 01791322247", "sixAmTech") ?>" /></td>
        </tr>
        <tr>
            <th><?php _e("Address", "sixAmTech") ?></th>
            <td><textarea name="address" required class="large-text"
                    placeholder="<?php _e("e.g: West kawnia, Barishal 8200", "sixAmTech") ?>"></textarea></td>
        </tr>
    </table>
    <button type="submit" class="button button-primary"><?php _e("Add contact", "sixAmTech") ?></button>
</form>
<?php
    }
}