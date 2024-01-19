<?php

namespace WordpressTema;

class Commands
{
    public static function init()
    {
        if (!class_exists('WP_CLI')) {
            return;
        }

        self::register_commands();
    }

    public static function register_commands()
    {
        \WP_CLI::add_command('ping', [self::class, 'ping']);
    }

    public function ping()
    {
        \WP_CLI::line('pong');
    }
}
