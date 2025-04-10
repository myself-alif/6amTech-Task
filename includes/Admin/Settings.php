<?php

namespace SixamTech\Admin;

class Settings
{

    private $option_group = 'sixamtech_task_settings_group';
    private $option_name = 'sixamtech_task_welcome_message';
    private $menu_slug = 'sixamtech-task-message';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }


    public function add_settings_page()
    {
        add_options_page(
            __('Welcome Message', 'sixAmTech'),
            __('6amTech Message', 'sixAmTech'),
            'manage_options',
            $this->menu_slug,
            [$this, 'render_settings_page']
        );
    }


    public function register_settings()
    {
        register_setting($this->option_group, $this->option_name, [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => ''
        ]);

        add_settings_section(
            'sixamtech_task_main_section',
            '',
            '__return_false',
            $this->menu_slug
        );

        add_settings_field(
            $this->option_name,
            __('Message:', 'sixAmTech'),
            [$this, 'render_textarea_field'],
            $this->menu_slug,
            'sixamtech_task_main_section'
        );
    }


    public function render_textarea_field()
    {
        $value = esc_textarea(get_option($this->option_name));
        echo '<textarea name="' . esc_attr($this->option_name) . '" rows="2" class="large-text code" placeholder="' . esc_attr__('Type welcome message here', 'sixAmTech') . '">' . $value . '</textarea>';
    }


    public function render_settings_page()
    {
?>
<div class="wrap">
    <h1><?php esc_html_e('Welcome Message', 'sixAmTech'); ?></h1>
    <form method="post" action="options.php">
        <?php
                settings_fields($this->option_group);
                do_settings_sections($this->menu_slug);
                submit_button();
                ?>
    </form>
</div>
<?php
    }
}