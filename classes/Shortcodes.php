<?php

namespace WordpressTema;

class Shortcodes
{
    public static function init()
    {
        self::my_dynamic_shorts();
    }

    public static function my_dynamic_shorts()
    {
        $short_dir = get_template_directory() . '/parts/shortcodes/';
        $myfiles = array_diff(scandir($short_dir), array('.', '..'));

        foreach ($myfiles as $file) {
            $name = str_replace(['.php'], '', $file);

            add_shortcode($name, function ($atts, $content = "") use ($name) {
                ob_start();

                get_template_part('parts/shortcodes/' . $name, null, [
                    'shortcode_attrs' => $atts,
                    'shortcode_content' => $content
                ]);
                
                $html = ob_get_contents();

                ob_end_clean();

                return $html;
            });
        }
    }
}
