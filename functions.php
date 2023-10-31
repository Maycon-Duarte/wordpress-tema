<?php
define("THEME_URL", get_stylesheet_directory_uri());
define("THEME_DIR", get_stylesheet_directory());

require_once THEME_DIR . '/vendor/autoload.php';

use WordpressTema\Api;
use WordpressTema\Config;
use WordpressTema\Customizer;
use WordpressTema\ElementorWidgets;
use WordpressTema\Log;
use WordpressTema\Notices;

Config::init();
Customizer::init();
ElementorWidgets::instance();
Api::init();

// Notices::add_notice('Teste de notice', 'info');
// Log::add('Teste de log', 'info');
