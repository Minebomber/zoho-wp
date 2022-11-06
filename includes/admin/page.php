<?php

namespace ZohoWP\Admin;

if (!defined('ABSPATH')) exit;

require_once ZOHOWP_DIR_PATH . '/includes/loader.php';

abstract class Page
{
	use \ZohoWP\Loader;

	protected const SLUG = null;

	abstract public static function admin_menu();
	abstract public static function admin_init();

	public static function render()
	{
		require(
			apply_filters(
				'zohowp_admin_partial',
				ZOHOWP_DIR_PATH . '/partials/admin/page.php',
				static::SLUG
			)
		);
	}

	protected static function add_submenu_page($page_title, $menu_title) {
		add_submenu_page('zohowp', $page_title, $menu_title, 'administrator', static::SLUG, [static::class, 'render']);
	}

	protected static function register_setting($name, $args = array()) {
		register_setting(static::SLUG, $name, $args);
	}

	protected static function add_section($id, $title, $callback)
	{
		add_settings_section($id, $title, [static::class, $callback], static::SLUG);
	}

	protected static function add_field($id, $title, $callback, $section)
	{
		add_settings_field($id, $title, [static::class, $callback], static::SLUG, $section);

	}
	protected static function render_field($args = array())
	{
		$attributes = array_merge([
			'type' => 'text'
		], $args);
		$result = join(' ', array_map(function ($key) use ($attributes) {
			if (is_bool($attributes[$key]))
				return $attributes[$key] ? $key : '';
			return $key . '="' . $attributes[$key] . '"';
		}, array_keys($attributes)));
?>
		<input <?php echo $result; ?> />
<?php
	}

	private function __construct()
	{
	}
}
