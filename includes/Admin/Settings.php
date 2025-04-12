<?php

namespace SixamTech\Admin;

class Settings
{
    private $option_group = 'sixamtech_task_settings_group';
    private $message_option_name = 'sixamtech_task_welcome_message';
    private $align_option_name = 'sixamtech_task_text_align';
    private $color_option_name = 'sixamtech_task_text_color';
    private $bg_color_option_name = 'sixamtech_task_bg_color';
    private $menu_slug = 'sixamtech-task-message';

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_page()
    {
        add_options_page(
            __('Welcome Message', 'sixAmTech'),
            __('6amTech Message', 'sixAmTech'),
            'manage_options',
            $this->menu_slug,
            array($this, 'render_settings_page')
        );
    }

    public function register_settings()
    {

        register_setting($this->option_group, $this->message_option_name, array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => ''
        ));


        register_setting($this->option_group, $this->align_option_name, array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_alignment'),
            'default' => 'left'
        ));


        register_setting($this->option_group, $this->color_option_name, array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#000000'
        ));

        // Register background color
        register_setting($this->option_group, $this->bg_color_option_name, array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#dddddd'
        ));

        add_settings_section(
            'sixamtech_task_main_section',
            '',
            '__return_false',
            $this->menu_slug
        );


        add_settings_field(
            $this->message_option_name,
            __('Message:', 'sixAmTech'),
            array($this, 'render_textarea_field'),
            $this->menu_slug,
            'sixamtech_task_main_section'
        );

        add_settings_field(
            $this->align_option_name,
            __('Text Alignment:', 'sixAmTech'),
            array($this, 'render_alignment_select_field'),
            $this->menu_slug,
            'sixamtech_task_main_section'
        );

        add_settings_field(
            $this->color_option_name,
            __('Text Color:', 'sixAmTech'),
            array($this, 'render_color_picker_field'),
            $this->menu_slug,
            'sixamtech_task_main_section'
        );

        add_settings_field(
            $this->bg_color_option_name,
            __('Background Color:', 'sixAmTech'),
            array($this, 'render_bg_color_picker_field'),
            $this->menu_slug,
            'sixamtech_task_main_section'
        );
    }

    public function sanitize_alignment($input)
    {
        $valid = array('left', 'center', 'right');
        return in_array($input, $valid, true) ? $input : 'left';
    }

    public function render_textarea_field()
    {
        $value = esc_textarea(get_option($this->message_option_name));
        echo '<textarea name="' . esc_attr($this->message_option_name) . '" rows="2" class="large-text code" placeholder="' . esc_attr__('Type welcome message here', 'sixAmTech') . '">' . $value . '</textarea>';
    }

    public function render_alignment_select_field()
    {
        $current = esc_attr(get_option($this->align_option_name, 'left'));
        $options = array('left' => 'Left', 'center' => 'Center', 'right' => 'Right');

        echo '<select name="' . esc_attr($this->align_option_name) . '">';
        foreach ($options as $value => $label) {
            $selected = selected($current, $value, false);
            echo "<option value='$value' $selected>$label</option>";
        }
        echo '</select>';
    }

    public function render_color_picker_field()
    {
        $value = esc_attr(get_option($this->color_option_name, '#000000'));
        echo '<input type="color" name="' . esc_attr($this->color_option_name) . '" value="' . $value . '">';
    }

    public function render_bg_color_picker_field()
    {
        $value = esc_attr(get_option($this->bg_color_option_name, '#dddddd'));
        echo '<input type="color" name="' . esc_attr($this->bg_color_option_name) . '" value="' . $value . '">';
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