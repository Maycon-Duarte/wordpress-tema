<?php

namespace Elementor;

class title_tag extends \Elementor\Widget_Base {

    public function get_name() {
        return 'title-tag';
    }

    public function get_title() {
        return 'Title Tag';
    }

    public function get_icon() {
        return 'eicon-code'; // https://elementor.github.io/elementor-icons/
    }

    public function get_categories() {
        return ['widgets-personalizados'];
    }

    public function get_style_depends()
    {
        return [str_replace(__NAMESPACE__ . '\\', '', __CLASS__)];
    }

    public function get_script_depends()
    {
        return [str_replace(__NAMESPACE__ . '\\', '', __CLASS__)];
    }

    protected function register_controls() {
        // adicione os controles do widget aqui
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="title-tag-widget">
            teste
        </div>
        <?php
    }

    protected function content_template() {
        // código do template do widget vai aqui
    }
}