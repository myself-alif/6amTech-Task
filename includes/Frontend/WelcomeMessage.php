<?php

namespace SixamTech\Frontend;

class WelcomeMessage
{
    public function __construct()
    {
        add_filter('the_content', [$this, 'prepend_welcome_message']);
    }
    public function prepend_welcome_message($content)
    {
        if (is_single() && is_main_query()) {
            // Get the message content
            $message = get_option('sixamtech_task_welcome_message', '');

            if (!empty($message)) {
                // Get the settings for text alignment, text color, and background color
                $alignment = get_option('sixamtech_task_text_align', 'left');
                $color = get_option('sixamtech_task_text_color', '#000000');
                $bg_color = get_option('sixamtech_task_bg_color', '#dddddd');

                // Manually build the style string
                $style = 'style="text-align:' . esc_attr($alignment) . '; ';
                $style .= 'color:' . esc_attr($color) . '; ';
                $style .= 'background-color:' . esc_attr($bg_color) . ';"';

                // Output the welcome message with the applied styles
                return '<div class="sixamtech-welcome-message" ' . $style . '>' . esc_html($message) . '</div>' . $content;
            }
        }

        return $content;
    }
}
