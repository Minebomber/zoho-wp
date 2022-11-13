<?php

namespace ZohoWP;

require_once ZOHOWP_DIR_PATH . '/includes/loader.php';
require_once ZOHOWP_DIR_PATH . '/includes/admin/general.php';
require_once ZOHOWP_DIR_PATH . '/includes/admin/oauth.php';
require_once ZOHOWP_DIR_PATH . '/includes/admin/cache.php';

class Admin
{
	use Loader;

	private function __construct()
	{
	}

	/**
 	 * Method run on init hook
	 */
	public static function init()
	{
		self::add_action('admin_menu', 'admin_menu');

		$classes = apply_filters('zohowp_admin_classes', [
			Admin\General::class,
			Admin\OAuth::class,
			Admin\Cache::class,
		]);

		foreach ($classes as $class) {
			$class::add_action('admin_init', 'admin_init');
			$class::add_action('admin_menu', 'admin_menu');
		}
	}

	/**
 	 * Method run on admin_menu hook
	 */
	public static function admin_menu()
	{
		add_menu_page(
			__('ZohoWP', 'zoho-wp'),
			__('ZohoWP', 'zoho-wp'),
			'administrator',
			'zohowp',
			'',
			'data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgNjQgNjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgZmlsbD0iY3VycmVudENvbG9yIj4KCTxwYXRoIGQ9Ik0gMCAwIEwgMCA2NCBMIDY0IDY0IEwgNjQgMCBMIDAgMCB6IE0gNDYuNjQ4NDM4IDEwLjM2MzI4MSBDIDQ2LjY0ODQzOCAxMC4zNjMyODEgNDkuNTA2MzUgMTAuNDIwNTcyIDQ5LjQ0OTIxOSAxNy4yMzgyODEgQyA0OS40MTAzNTkgMjEuODc1NTgyIDUwLjI2MTA5MyAyMy44MjYyNjQgNDMuOTY0ODQ0IDMxLjc2MTcxOSBDIDM3LjY2ODU5MyAzOS42OTcxNzQgMzEuMTg5NDUzIDQ0LjUyMTQ4NCAzMS4xODk0NTMgNDQuNTIxNDg0IEMgMzEuMTg5NDUzIDQ0LjUyMTQ4NCAyOS40NDg5MTUgNDYuMTA5MzQ1IDMyIDQ2LjMxMDU0NyBDIDMzLjI1MDQ5NCA0Ni40MDkxNzcgNDQuNzQ0MTQxIDQ2LjMxMDU0NyA0NC43NDQxNDEgNDYuMzEwNTQ3IEMgNDQuNzQ0MTQxIDQ2LjMxMDU0NyA0OC40MDA0NTggNDYuMjg5NDI1IDQ5LjQxNDA2MiA1MS45Mzk0NTMgQyA1MC4yMDg1NzUgNTYuMzY4MjEyIDQ3LjY1ODIwMyA1Ni4xMTMyODEgNDcuNjU4MjAzIDU2LjExMzI4MSBMIDIwLjg5ODQzOCA1Ni4xNzU3ODEgQyAyMC44OTg0MzggNTYuMTc1NzgxIDE4Ljc1MTgwOCA1Ni4wMzczMDUgMTguMjY5NTMxIDUzLjQ3NDYwOSBDIDE3Ljc4NzI1NiA1MC45MTE5MTQgMTcuOTgwNDY5IDQ3LjQ1ODk4NCAxNy45ODA0NjkgNDcuNDU4OTg0IEMgMTcuOTgwNDY5IDQ3LjQ1ODk4NCAxNy41MzM2MDggNDMuMDA1NjIyIDE5LjcwNTA3OCAzOS4wOTU3MDMgQyAyMC4yNjE2NDcgMzguMDkzNTU0IDM1LjkwNjI1IDIyLjgxNDQ1MyAzNS45MDYyNSAyMi44MTQ0NTMgQyAzNS45MDYyNSAyMi44MTQ0NTMgMzcuMzI1NDE4IDIwLjY3MTIyNyAzNS42MzY3MTkgMjAuNjg1NTQ3IEMgMzMuOTQ4MDE5IDIwLjY5OTg2NyAyMy40NTExNzIgMjAuNjg1NTQ3IDIzLjQ1MTE3MiAyMC42ODU1NDcgQyAyMy40NTExNzIgMjAuNjg1NTQ3IDE5LjQ0MDU1NiAyMS4xNDkzMjQgMTkuMjI2NTYyIDE1LjAwMzkwNiBDIDE5LjA2NDg5NCAxMC4zNjExNDIgMjEuNjIzMDQ3IDEwLjM3NSAyMS42MjMwNDcgMTAuMzc1IEwgNDYuNjQ4NDM4IDEwLjM2MzI4MSB6IiAvPgo8L3N2Zz4K'
		);
	}
}
