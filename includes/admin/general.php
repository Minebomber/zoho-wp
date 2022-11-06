<?php

namespace ZohoWP\Admin;

if (!defined('ABSPATH')) exit;

require_once ZOHOWP_DIR_PATH . '/includes/admin/page.php';

class General extends Page
{
	protected const SLUG = 'zohowp';

	public static function admin_menu()
	{
		self::add_submenu_page(
			__('Zoho for Wordpress', 'zoho-wp'),
			__('General', 'zoho-wp'),
		);
	}

	public static function admin_init()
	{
		self::add_filter('zohowp_admin_partial', 'admin_partial', 0, 2);
	}

	public static function admin_partial($partial, $slug)
	{
		if ($slug === static::SLUG)
			return ZOHOWP_DIR_PATH . '/partials/admin/general.php';
		return $partial;
	}
}
