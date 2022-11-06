<?php

namespace ZohoWP;

require_once ZOHOWP_DIR_PATH . '/includes/trait-loader.php';
require_once ZOHOWP_DIR_PATH . '/includes/class-admin.php';

class Plugin
{
	use Loader;

	private static $_instance = null;
	public static function instance() {
		if (is_null(self::$_instance))
			self::$_instance = new self();
		return self::$_instance;
	}

	private function __construct()
	{
		self::add_action('init', 'init');
		Admin::add_action('init', 'init');
	}

	public static function init()
	{
		load_plugin_textdomain('zoho-wp', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
}
