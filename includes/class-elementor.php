<?php

namespace ZohoWP;

require_once ZOHOWP_DIR_PATH . '/includes/trait-singleton.php';

class Elementor {
	use Singleton;

	public function register_form_actions($registrar) {
		require_once ZOHOWP_DIR_PATH . '/includes/elementor/subscribe-action.php';
		$registrar->register(new \ZohoWP_Subscribe_Action());
	}
}
