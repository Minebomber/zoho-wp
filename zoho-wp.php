<?php
/*
Plugin Name: Zoho for WordPress
Description: Connects Wordpress to the Zoho API and adds a form submission action to Elementor Pro forms
Version: 1.0.2
Author: Mark Lagae
Text Domain: zoho-wp
*/

define('ZOHOWP_DIR_PATH', plugin_dir_path(__FILE__));

require_once ZOHOWP_DIR_PATH . '/includes/class-plugin.php';
\ZohoWP\Plugin::instance()->run();
