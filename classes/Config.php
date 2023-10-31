<?php

namespace WordpressTema;

class Config
{
    use Hooker;

    protected static $actions = [
        'after_setup_theme' => 'init',
        'wp_enqueue_scripts' => 'register_assets',
    ];

    public static function init()
    {
        self::register_hooks();
        self::config();

        add_theme_support('post-thumbnails');

        if (!current_user_can('administrator') && !is_admin()) {
            show_admin_bar(false);
        }
    }

    private static function config()
    {
        $config_file = THEME_DIR . DIRECTORY_SEPARATOR . "config.php";

        if (file_exists($config_file)) {
            $config = include $config_file;
        }

        if ($config === null) {
            return;
        }

        foreach ($config['nav_menus'] as $key => $menu) {
            register_nav_menu($key, $menu);
        }
    }

    public static function register_assets()
    {
        wp_register_style("theme-style", THEME_URL . "/style.css?rand=" . rand(10, 10000));
        wp_register_script("theme-script", THEME_URL . "/scripts.js?rand=" . rand(10, 10000), ['jquery', 'bootstrap', 'splide', 'lottie-web'], false, true);
        wp_register_style("bootstrap", THEME_URL . "/node_modules/bootstrap/dist/css/bootstrap.min.css", [], false, false);
        wp_register_script("bootstrap", THEME_URL . "/node_modules/bootstrap/dist/js/bootstrap.min.js", ['jquery'], false, true);
        wp_register_style("bootstrap-icons", THEME_URL . "/node_modules/bootstrap-icons/font/bootstrap-icons.css", [], false, false);
        wp_register_script("splide", THEME_URL . "/node_modules/@splidejs/splide/dist/js/splide.min.js", [], false, true);
        wp_register_style("splide", THEME_URL . "/node_modules/@splidejs/splide/dist/css/splide.min.css", [], false, false);
        wp_register_script("lottie-web", THEME_URL . "/node_modules/lottie-web/build/player/lottie.min.js", [], false, true);


        self::init_scripts();
    }

    private static function init_scripts()
    {
        if (is_admin()) {
            return;
        }
        wp_enqueue_style('theme-style');
        wp_enqueue_script('theme-script');
        wp_enqueue_style('bootstrap');
        wp_enqueue_script('bootstrap');
        wp_enqueue_style('bootstrap-icons');
        wp_enqueue_script('splide');
        wp_enqueue_style('splide');
        wp_enqueue_script('lottie-web');
    }
}
