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
            //only adds scripts to the custom admin menu
            wp_enqueue_script("contact_script", plugins_url('/6amtech-task/assets/js/scripts.js'), array('jquery'), false, true);
            wp_localize_script('contact_script', 'API_DATA', array(
                'nonce' => wp_create_nonce('wp_rest'),
                'url'   => rest_url('6amTech/v1/contacts/')
            ));
            wp_enqueue_style('bootstrap-css', '//cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css');
            wp_enqueue_style('toastr-css', '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css');
            wp_enqueue_script('toastr-js', '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js', array('jquery'), null, true);
            wp_enqueue_style('admin-style-css', plugins_url('/6amtech-task/assets/css/admin.css'), null, false);
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
    public function shortcode()
    {
?>
        <div class="p-3 d-flex flex-column justify-content-center align-items-center" style="gap:8px">
            <strong><?php _e('Shortcode:', 'sixAmTech'); ?></strong>
            <code>[sixamtech_contacts]</code>
            <p><?php _e('Use this shortcode to display the contact list on any post or page.', 'sixAmTech'); ?></p>
        </div>
    <?php
    }
    public function contacts_page()
    {
        $this->shortcode();
        $this->render_contacts_list();
        echo '</div>';
    }
    public function add_new_contact_page()
    {
        echo '<div class="wrap"><h1>' . __("Add New Contact", "sixAmTech") . '</h1>';
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
        ob_start();
    ?>
        <div class="wrap">
            <h1><?php _e('Contact List', 'sixAmTech'); ?></h1>
            <table class="widefat fixed striped">
                <thead>
                    <tr class="bg-dark">
                        <th class="font-weight-bold text-light"><?php _e('Name', 'sixAmTech'); ?></th>
                        <th class="font-weight-bold text-light"><?php _e('Email', 'sixAmTech'); ?></th>
                        <th class="font-weight-bold text-light"><?php _e('Mobile', 'sixAmTech'); ?></th>
                        <th class="font-weight-bold text-light"><?php _e('Address', 'sixAmTech'); ?></th>
                        <th class="font-weight-bold text-light"><?php _e('Actions', 'sixAmTech'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($contacts)) {
                        foreach ($contacts as $contact) { ?>
                            <tr>
                                <td class="editable id" style="display:none"><?php echo esc_html($contact->id); ?></td>
                                <td class="editable name"><?php echo esc_html($contact->name); ?></td>
                                <td class="editable email"><?php echo esc_html($contact->email); ?></td>
                                <td class="editable mobile"><?php echo esc_html($contact->mobile); ?></td>
                                <td class="editable address"><?php echo esc_html($contact->address); ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary edit-contact"
                                        data-id="<?php echo esc_attr($contact->id); ?>">
                                        <?php _e('Edit', 'sixAmTech'); ?>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-success data-update" style="display:none"
                                        data-id="<?php echo esc_attr($contact->id); ?>">
                                        <?php _e('Update', 'sixAmTech'); ?>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-danger delete-contact"
                                        data-id="<?php echo esc_attr($contact->id); ?>">
                                        <?php _e('Delete', 'sixAmTech'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="5"><?php _e('No contacts found.', 'sixAmTech'); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
            $total_pages = ceil($total / $per_page);
            if ($total_pages > 1) { ?>
                <div class="tablenav d-flex justify-content-center align-items-center">
                    <div class="tablenav-pages">
                        <?php
                        echo paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => __('« Prev', 'sixAmTech'),
                            'next_text' => __('Next »', 'sixAmTech'),
                            'total' => $total_pages,
                            'current' => $current_page
                        ));
                        ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php
        echo ob_get_clean();
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
