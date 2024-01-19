<?php

namespace WordpressTema;

class Config
{
    use Hooker;

    protected static $actions = [
        'after_setup_theme' => 'init',
        'wp_enqueue_scripts' => 'register_assets',
        'admin_enqueue_scripts' => 'register_admin_assets',
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
        $config_file = THEME_DIR . "/config.php";

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

        wp_register_script("theme-script", THEME_URL . "/scripts.js?rand=" . rand(10, 10000), ['bootstrap'], false, true);
            wp_register_script("bootstrap", THEME_URL . "/assets/lib/bootstrap/dist/js/bootstrap.min.js", ['my-jquery'], false, true);
            wp_register_script('my-jquery', THEME_URL . '/assets/lib/jquery/dist/jquery.min.js', [], false, true);
            wp_localize_script('theme-script', 'THEME', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'home_url' => home_url(),
                'theme_url' => THEME_URL,
                'theme_dir' => THEME_DIR,
            ]);
        self::init_scripts();
    }

    public static function register_admin_assets(){
        // wp_register_style("theme-style-admin", THEME_URL . "/assets/css/admin.css?rand=" . rand(10, 10000));
 
        self::init_scripts_admin();
    }

    private static function init_scripts()
    {
        wp_enqueue_style('theme-style');
        wp_enqueue_script('theme-script');
    }

    private static function init_scripts_admin()
    {
        // wp_enqueue_style('theme-style-admin');
    }
}
