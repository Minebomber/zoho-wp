<?php
/*
Plugin Name: Zoho for WordPress
Description: Connects Wordpress to the Zoho API
Version: 1.0.1
Author: Mark Lagae
Text Domain: zoho-wp
*/

define('ZOHOWP_DIR_PATH', plugin_dir_path(__FILE__));
define('ZOHOWP_VERSION', '1.0.1');

require_once ZOHOWP_DIR_PATH . '/includes/plugin.php';
\ZohoWP\Plugin::instance();
