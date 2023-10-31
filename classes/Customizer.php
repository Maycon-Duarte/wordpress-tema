<?php

namespace WordpressTema;

class Customizer
{
    public static function init()
    {
        if (!class_exists('Kirki')) {
            Notices::add_notice('Kirki customizer não está instalado', 'warning');
            return;
        }

        self::add_panel();
        self::tagline_section_reset();
    }

    public static function add_panel()
    {
        new \Kirki\Panel(
            'customizer_panel',
            [
                'priority'    => 10,
                'title'       => esc_html__('Personalizações do site', 'kirki'),
                'description' => esc_html__('Personalizações do site.', 'kirki'),
            ]
        );
    }

    public static function tagline_section_reset()
    {
        new \Kirki\Field\Image(
            [
                'settings'    => 'custom_logo',
                'label'       => esc_html__('Logo', 'kirki'),
                'section'     => 'title_tagline',
                'default'     => '',
                'priority'    => 8,
            ]
        );
        new \Kirki\Field\Image(
            [
                'settings'    => 'logo_dark',
                'label'       => esc_html__('Logo dark', 'kirki'),
                'section'     => 'title_tagline',
                'default'     => '',
                'priority'    => 9,
            ]
        );
    }
}
