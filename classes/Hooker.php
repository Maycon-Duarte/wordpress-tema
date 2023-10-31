<?php

namespace WordpressTema;

trait Hooker
{
    public static function register_hooks()
    {
        static::registerActions();
        static::registerFilters();
        static::registerShortcodes();
    }

    protected static function registerActions()
    {
        if (property_exists(static::class, 'actions')) {
            foreach (static::$actions as $hook => $callback) {
                $hook = is_numeric($hook) ? $callback : $hook;
                $callback = is_numeric($hook) ? $hook : $callback;

                if (is_array($callback)) {
                    list($function, $priority, $args) = static::parseHookCallback($callback);
                    add_action($hook, [static::class, $function], $priority, $args);
                } elseif (is_string($callback)) {
                    add_action($hook, [static::class, $callback]);
                }
            }
        }
    }

    protected static function registerFilters()
    {
        if (property_exists(static::class, 'filters')) {
            foreach (static::$filters as $hook => $callback) {
                $hook = is_numeric($hook) ? $callback : $hook;
                $callback = is_numeric($hook) ? $hook : $callback;

                if (is_array($callback)) {
                    list($function, $priority, $args) = static::parseHookCallback($callback);
                    add_filter($hook, [static::class, $function], $priority, $args);
                } elseif (is_string($callback)) {
                    add_filter($hook, [static::class, $callback]);
                }
            }
        }
    }

    protected static function registerShortcodes()
    {
        if (property_exists(static::class, 'shortcodes')) {
            foreach (static::$shortcodes as $shortcode => $callback) {
                if (is_string($shortcode) && (is_string($callback) || is_callable($callback))) {
                    add_shortcode($shortcode, [static::class, $callback]);
                }
            }
        }
    }

    protected static function parseHookCallback(array $callback)
    {
        $function = $callback[0] ?? '';
        $priority = $callback[1] ?? 10;
        $args = $callback[2] ?? 1;

        return [$function, $priority, $args];
    }
}
