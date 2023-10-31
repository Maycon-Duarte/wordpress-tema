<?php

namespace WordpressTema;

class ElementorWidgets
{
    const MINIMUM_ELEMENTOR_VERSION = '3.15.1';
    const MINIMUM_PHP_VERSION = '7.4';

    const CATEGORY_ARRAY = [
        'widgets-personalizados' => [
            'title' => 'Widgets Personalizados',
            'icon' => 'eicon-global-colors',
        ],
    ];

    // const WIDGETS_PERMITIDOS = [];

    public static $_instance = null;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        if ($this->is_compatible()) {
            add_action('elementor/init', [$this, 'init']);
        }
    }

    public function is_compatible()
    {
        // Verifica se o Elementor está instalado e ativo
        if (!did_action('elementor/loaded')) {
            Notices::add_notice('O elemento não está instalado ou ativado.', 'error');
            return false;
        }

        // Verifica a versão do Elementor
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            Notices::add_notice('O tema requer a versão ' . self::MINIMUM_ELEMENTOR_VERSION . ' ou superior do Elementor.', 'error');
            return false;
        }

        // Verifica a versão do PHP
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            Notices::add_notice('O tema requer a versão ' . self::MINIMUM_PHP_VERSION . ' ou superior do PHP.', 'error');
            return false;
        }

        return true;
    }


    public function init()
    {
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'frontend_styles']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'frontend_scripts']);
        add_action('elementor/elements/categories_registered', [$this, 'register_category']);
        add_action('elementor/widgets/register', [$this, 'unregister_widgets'], 20);
        add_action('elementor/widgets/register', [$this, 'register_widgets'], 20);

        $this->register_commands();
    }

    public function frontend_styles()
    {
        $widget_styles = glob(get_template_directory() . '/assets/css/widgets/*.css');
        foreach ($widget_styles as $widget_style) {
            $widget_name = basename($widget_style, '.css');
            wp_register_style(str_replace('-', '_', $widget_name), get_template_directory_uri() . '/assets/css/widgets/' . $widget_name . '.css');
            //wp_enqueue_style(str_replace('-', '_', $widget_name));
        }
    }

    public function frontend_scripts()
    {
        $widget_scripts = glob(get_template_directory() . '/assets/js/widgets/*.js');
        foreach ($widget_scripts as $widget_script) {
            $widget_name = basename($widget_script, '.js');
            wp_register_script(str_replace('-', '_', $widget_name), get_template_directory_uri() . '/assets/js/widgets/' . $widget_name . '.js', [], false, true);
            //wp_enqueue_script($widget_name);
        }
    }

    public function register_category($elements_manager)
    {
        foreach (self::CATEGORY_ARRAY as $category_slug => $category) {
            $elements_manager->add_category($category_slug, $category);
        }
    }

    public function unregister_widgets($widgets_manager)
    {
        if (!defined('self::WIDGETS_PERMITIDOS')) {
            return;
        }

        $widgets = $widgets_manager->get_widget_types();
        foreach ($widgets as $widget) {
            if ($widget->get_name() === 'common' || in_array($widget->get_name(), self::WIDGETS_PERMITIDOS)) {
                continue;
            }
            $widgets_manager->unregister($widget->get_name());
        }
    }

    public function register_widgets($widgets_manager)
    {
        $widget_files = glob(get_template_directory() . '/parts/widgets/*.php');

        foreach ($widget_files as $widget_file) {
            require_once($widget_file);
            // Obtenha o nome da classe do arquivo de widget
            $widget_name = basename($widget_file, '.php');
            $widget_name = str_replace('-', '_', ucwords($widget_name, '-'));

            // Crie uma instância do widget e registre no widgets_manager
            $widget_class = '\\Elementor\\' . basename($widget_name, '.php');

            if (!class_exists($widget_class)) {
                var_dump($widget_class);
                continue;
            }

            $widget_instance = new $widget_class();
            $widgets_manager->register($widget_instance);
        }
    }

    public function register_commands()
    {
        if (!class_exists('WP_CLI')) return;
        \WP_CLI::add_command('create-widget', [$this, 'create_widget_command']);
    }

    public function create_widget_command($args)
    {
        if (!isset($args[0])) return \WP_CLI::error("widget name is required");
        $widget_name = $args[0];

        // Remove acentos e caracteres especiais
        $args[0] = preg_replace('/[^a-zA-Z0-9]/', '-', $args[0]);

        $widget_class = str_replace('-', '_', strtolower($args[0]));
        $widget_name = str_replace('_', '-', strtolower($args[0]));
        $widget_title = ucwords(str_replace('-', ' ', $args[0]));

        // Carrega o template do widget
        $widget_content = $this->get_widget_template($widget_class, $widget_name, $widget_title);

        // Caminho para o diretório de widgets do tema
        $widget_dir = './parts/widgets';

        // Verifica e cria o diretório se não existir
        if (!file_exists($widget_dir)) {
            mkdir($widget_dir);
        }

        // Verifica se o widget já existe
        if (file_exists($widget_dir . '/' . $widget_name . '.php')) {
            \WP_CLI::error("Widget {$widget_name}.php already exists in {$widget_dir}");
        }

        // Caminho para o arquivo do widget
        $widget_file = $widget_dir . '/' . $widget_name . '.php';

        // Cria o arquivo do widget
        file_put_contents($widget_file, $widget_content);

        // Adiciona o SCSS do widget
        $widget_scss = '.' . $widget_name . '-widget {}';

        $widget_scss_file = get_template_directory() . '/assets/scss/widgets/' . $widget_name . '.scss';
        file_put_contents($widget_scss_file, $widget_scss);

        \WP_CLI::success("Widget '{$widget_name}' created successfully in {$widget_file}");
    }

    public function get_widget_template($widget_class, $widget_name, $widget_title)
    {
        $template = <<<EOT
        <?php
        
        namespace Elementor;
        
        class $widget_class extends \Elementor\Widget_Base {
        
            public function get_name() {
                return '$widget_name';
            }
        
            public function get_title() {
                return '$widget_title';
            }
        
            public function get_icon() {
                return 'eicon-code'; // https://elementor.github.io/elementor-icons/
            }
        
            public function get_categories() {
                return ['widgets-personalizados'];
            }
        
            public function get_style_depends()
            {
                return [str_replace(__NAMESPACE__ . '\\\', '', __CLASS__)];
            }
        
            public function get_script_depends()
            {
                return [str_replace(__NAMESPACE__ . '\\\', '', __CLASS__)];
            }
        
            protected function register_controls() {
                // adicione os controles do widget aqui
            }
        
            protected function render() {
                \$settings = \$this->get_settings_for_display();
                ?>
                <div class="$widget_name-widget">
                    <!-- conteudo do widget vai aqui -->
                </div>
                <?php
            }
        
            protected function content_template() {
                // código do template do widget vai aqui
            }
        }
        EOT;
        return $template;
    }
}
