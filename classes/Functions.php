<?php

namespace WordpressTema;

use WP_Query;

class Functions
{
    use Hooker;

    protected static $actions = [];
    protected static $filters = [];

    public static function init()
    {
        self::register_hooks();
    }
}
