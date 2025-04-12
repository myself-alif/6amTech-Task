<?php

namespace SixamTech\Frontend;

class Shortcode
{
    public function __construct()
    {
        add_shortcode('sixamtech_contacts', array($this, 'render_contacts_list'));
    }

    public function render_contacts_list()
    {
        global $wpdb;

        $table_name = 'contact_list';
        $per_page = 5;

        // Use custom query param to avoid permalink conflicts
        $current_page = isset($_GET['contact_page']) ? max(1, intval($_GET['contact_page'])) : 1;
        $offset = ($current_page - 1) * $per_page;

        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

        $contacts = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_name ORDER BY id ASC LIMIT %d OFFSET %d", $per_page, $offset)
        );

        ob_start();
?>
<div class="wrap">
    <h3><?php _e('Contact List', 'sixAmTech'); ?></h3>
    <div style="overflow-x: auto; width: 100%;">
        <table width="100%" cellpadding="8" cellspacing="0"
            style="table-layout: fixed; min-width: 600px; border-color:#ddd">
            <thead>
                <tr style="color: white; background-color: black">
                    <th style="word-wrap: break-word;"><?php _e('Name', 'sixAmTech'); ?></th>
                    <th style="word-wrap: break-word;"><?php _e('Email', 'sixAmTech'); ?></th>
                    <th style="word-wrap: break-word;"><?php _e('Mobile', 'sixAmTech'); ?></th>
                    <th style="width: 30%; word-wrap: break-word;"><?php _e('Address', 'sixAmTech'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contacts)) {
                            foreach ($contacts as $contact) { ?>
                <tr>
                    <td style="word-wrap: break-word;"><?php echo esc_html($contact->name); ?></td>
                    <td style="word-wrap: break-word;"><?php echo esc_html($contact->email); ?></td>
                    <td style="word-wrap: break-word;"><?php echo esc_html($contact->mobile); ?></td>
                    <td style="word-wrap: break-word;"><?php echo esc_html($contact->address); ?></td>
                </tr>
                <?php }
                        } else { ?>
                <tr>
                    <td colspan="4"><?php _e('No contacts found.', 'sixAmTech'); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php
            $total_pages = ceil($total / $per_page);
            if ($total_pages > 1) { ?>
    <div style="text-align: center; margin-top: 20px;">
        <div>
            <?php
                        echo paginate_links([
                            'base' => add_query_arg('contact_page', '%#%'),
                            'format' => '',
                            'prev_text' => __('« Prev', 'sixAmTech'),
                            'next_text' => __('Next »', 'sixAmTech'),
                            'total' => $total_pages,
                            'current' => $current_page,
                            'type' => 'plain'
                        ]);
                        ?>
        </div>
    </div>
    <?php } ?>
</div>
<?php
        return ob_get_clean();
    }
}