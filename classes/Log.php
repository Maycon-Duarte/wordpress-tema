<?php

namespace WordpressTema;

class Log
{
    public static function add($item, $append = true, $log = 'debug.log')
    {
        self::create_log($log);

        $hora = wp_date('d/m/Y H:i:s');

        if ($append) {
            file_put_contents(get_stylesheet_directory() . '/logs/' . $log, print_r($hora . ' - ' . $item, true) . "\n", FILE_APPEND);
        } else {
            file_put_contents(get_stylesheet_directory() . '/logs/' . $log, print_r($hora . ' - ' . $item, true) . "\n");
        }
    }

    private static function create_log($log = 'debug.log')
    {
        if (file_exists(get_stylesheet_directory() . '/logs')) {
            if (!file_exists(get_stylesheet_directory() . '/logs/' . $log)) {
                file_put_contents(get_stylesheet_directory() . '/logs/' . $log, '');
            }
        } else {
            mkdir(get_stylesheet_directory() . '/logs');
            file_put_contents(get_stylesheet_directory() . '/logs/' . $log, '');
        }
    }
}
