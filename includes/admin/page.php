<?php

namespace ZohoWP\Admin;

if (!defined('ABSPATH')) exit;

require_once ZOHOWP_DIR_PATH . '/includes/loader.php';

abstract class Page
{
	use \ZohoWP\Loader;

	/** Admin page slug */
	protected const SLUG = null;

	/**
	 * Method runs on admin_init hook
	 */
	abstract public static function admin_init();
	/**
	 * Method runs on admin_menu hook
	 */
	abstract public static function admin_menu();

	/**
	 * Render the admin page
	 */
	public static function render()
	{
		require(apply_filters(
				'zohowp_admin_partial',
				ZOHOWP_DIR_PATH . '/partials/admin/page.php',
				static::SLUG
			)
		);
	}

	/**
	 * Wrapper for add_submenu_page
	 */
	protected static function add_submenu_page($page_title, $menu_title)
	{
		add_submenu_page('zohowp', $page_title, $menu_title, 'administrator', static::SLUG, [static::class, 'render']);
	}

	/**
	 * Wrapper for register_setting
	 */
	protected static function register_setting($name, $args = array())
	{
		register_setting(static::SLUG, $name, $args);
	}

	/**
	 * Wrapper for add_settings_section
	 */
	protected static function add_section($id, $title, $callback)
	{
		add_settings_section($id, $title, [static::class, $callback], static::SLUG);
	}

	/**
	 * Wrapper for add_settings_field
	 */
	protected static function add_field($id, $title, $callback, $section, $args = array())
	{
		add_settings_field($id, $title, [static::class, $callback], static::SLUG, $section, $args);
	}

	/**
	 * Render an HTML input field
	 * @param array @args Attributes for the input
	 */
	protected static function render_field($args = array())
	{
		$attributes = array_merge([
			'type' => 'text'
		], $args);
		$result = self::html_attributes($attributes);
?>
		<input <?php echo $result; ?> />
<?php
	}

	/**
	 * Convert an array to a string of html attributes
	 * @param array $attributes Array of attributes
	 * @return string Attributes formatted as HTML
	 */
	protected static function html_attributes($attributes) {
		return join(' ', array_map(function ($key) use ($attributes) {
			if (is_bool($attributes[$key]))
				return $attributes[$key] ? $key : '';
			return $key . '="' . $attributes[$key] . '"';
		}, array_keys($attributes)));
	}

	private function __construct()
	{
	}
}
