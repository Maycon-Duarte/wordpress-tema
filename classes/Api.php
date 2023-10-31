<?php

namespace WordpressTema;

class Api
{
    use Hooker;

    protected static $actions = [
        'rest_api_init' => 'rest_init',
    ];

    public static function init()
    {
        self::register_hooks();
    }

    public static function rest_init()
    {
        register_rest_route('api/v1', 'teste', [
            'methods' => ['GET'],
            'callback' => [self::class, 'teste'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function teste()
    {
        return [
            'status' => 'ok',
            'message' => 'Hello World',
        ];
    }
}
