<?php

namespace WordpressTema;

class Notices
{
    use Hooker;

    protected static $actions = [
        'admin_notices' => 'admin_notices',
    ];

    private static $success = [];
    private static $info = [];
    private static $warning = [];
    private static $error = [];

    private static function alert($messages = [], $type = 'info')
    {
        if (empty($messages)) {
            $messages = self::$$type;
        }

        $class = 'notice notice-' . $type;

        foreach ($messages as $message) {
            printf('<div class="%s"><p>%s</p></div>', esc_attr($class), esc_html($message));
        }
    }

    public static function admin_notices()
    {
        self::alert(self::$success, 'success');
        self::alert(self::$info, 'info');
        self::alert(self::$warning, 'warning');
        self::alert(self::$error, 'error');
    }

    public static function add_notice($message, $type = 'info')
    {
        self::$$type[] = $message;
        self::register_hooks();
    }
}
