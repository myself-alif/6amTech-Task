<?php

namespace SixamTech;

class Activator
{
    public static function activate()
    {
        add_option('sixamtech_task_welcome_message', 'Welcome to our site!');
    }
}