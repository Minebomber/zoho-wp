<?php
namespace ZohoWP;

require_once ZOHOWP_DIR_PATH . '/includes/trait-singleton.php';

class Plugin {
	use Singleton;

	protected $loader;

	protected function __construct() {
		$this->load_dependencies();
		$this->load_i18n();
		$this->load_admin();
		$this->load_public();
		$this->load_elementor();
	}

	private function load_dependencies() {
		require_once ZOHOWP_DIR_PATH . '/includes/class-loader.php';
		require_once ZOHOWP_DIR_PATH . '/includes/class-i18n.php';
		require_once ZOHOWP_DIR_PATH . '/includes/class-admin.php';
		require_once ZOHOWP_DIR_PATH . '/includes/class-zoho.php';
		require_once ZOHOWP_DIR_PATH . '/includes/class-elementor.php';

		$this->loader = Loader::instance();
	}

	private function load_i18n() {
		$i18n = I18N::instance();
		$this->loader->add_action('init', $i18n, 'load_plugin_textdomain');
	}

	private function load_admin() {
		$admin = Admin::instance();
		$this->loader->add_action('admin_menu', $admin, 'add_admin_menu');
		$this->loader->add_action('admin_init', $admin, 'register_settings');
	}

	private function load_elementor() {
		$elementor = Elementor::instance();
		$this->loader->add_action('elementor_pro/forms/actions/register', $elementor, 'register_form_actions');
	}

	private function load_public() {
		add_shortcode('mailinglists', [$this, 'test_mailinglists']);
	}

	public function test_mailinglists() {
		return '<pre>' . json_encode(Zoho::instance()->get_all_fields()) . '</pre>';
	}
	

	public function run() {
		$this->loader->register_all();
	}
}
