<?php

namespace ZohoWP;

require_once ZOHOWP_DIR_PATH . '/includes/trait-singleton.php';

class I18N {
	use Singleton;

	public function load_plugin_textdomain() {
		load_plugin_textdomain('zoho-wp', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
}
