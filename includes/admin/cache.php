<?php

namespace ZohoWP\Admin;

if (!defined('ABSPATH')) exit;

require_once ZOHOWP_DIR_PATH . '/includes/admin/page.php';
require_once ZOHOWP_DIR_PATH . '/includes/api/oauth.php';

class Cache extends Page
{
	protected const SLUG = 'zohowp-cache';

	public static function admin_menu()
	{
		self::add_submenu_page(
			__('ZohoWP Cache', 'zoho-wp'),
			__('Cache', 'zoho-wp'),
		);
	}

	public static function admin_init()
	{
		// Register settings
		self::register_setting('zohowp_cache_purge', ['type' => 'string', 'default' => '', 'sanitize_callback' => [static::class, 'purge_cache'] ]);
		// Add sections & fields
		self::add_section(
			'purge',
			__('Purge Cache', 'zoho-wp'),
			'purge_section'
		);
		self::add_field(
			'access_token',
			__('Access Token', 'zoho-wp'),
			'purge_field',
			'purge',
			['key' => 'access_token']
		);
		self::add_field(
			'user_schema',
			__('User Schema', 'zoho-wp'),
			'purge_field',
			'purge',
			['key' => 'all_fields']
		);
		self::add_field(
			'mailing_lists',
			__('Mailing Lists', 'zoho-wp'),
			'purge_field',
			'purge',
			['key' => 'mailing_lists']
		);
	}

	public static function purge_cache($input) {
		foreach ($input as $key) {
			switch ($key) {
			case 'access_token':
				delete_transient('zohowp_access_token');
				break;

			case 'all_fields':
				delete_transient('zohowp_cache_all_fields');
				break;

			case 'mailing_lists':
				delete_transient('zohowp_cache_mailing_lists');
				break;
			}
		}
		return '';
	}

	/**
	 *
	 */
	public static function purge_section()
	{
	?>
		<p><?php _e('Select values to purge from the cache', 'zoho-wp'); ?></p>
<?php
	}

	/**
	 */
	public static function purge_field($args)
	{
		self::render_field([
			'type' => 'checkbox',
			'id' => "zohowp_cache_purge_{$args['key']}",
			'name' => 'zohowp_cache_purge[]',
			'value' => $args['key'],
		]);
	}
}
