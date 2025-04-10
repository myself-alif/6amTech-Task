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
            $message = get_option('sixamtech_task_welcome_message', '');
            if (!empty($message)) {
                return '<div class="sixamtech-welcome-message">' . esc_html($message) . '</div>' . $content;
            }
        }
        return $content;
    }
}