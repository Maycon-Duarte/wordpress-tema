<?php
define("THEME_URL", get_stylesheet_directory_uri());
define("THEME_DIR", get_stylesheet_directory());

require_once THEME_DIR . '/vendor/autoload.php';

use WordpressTema\Api;
use WordpressTema\Commands;
use WordpressTema\Config;
use WordpressTema\Customizer;
use WordpressTema\ElementorWidgets;
use WordpressTema\Functions;
use WordpressTema\Log;
use WordpressTema\Notices;
use WordpressTema\Shortcodes;

Config::init();
Customizer::init();
Functions::init();
Shortcodes::init();
//ElementorWidgets::instance();
Api::init();
Commands::init();

// Notices::add_notice('Teste de notice', 'info');
// Log::add('Teste de log', 'info');
