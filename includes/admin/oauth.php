<?php

namespace ZohoWP\Admin;

if (!defined('ABSPATH')) exit;

require_once ZOHOWP_DIR_PATH . '/includes/admin/page.php';
require_once ZOHOWP_DIR_PATH . '/includes/api/oauth.php';

class OAuth extends Page
{
	protected const SLUG = 'zohowp-oauth';

	public static function admin_menu()
	{
		self::add_submenu_page(
			__('ZohoWP OAuth', 'zoho-wp'),
			__('OAuth', 'zoho-wp'),
		);
	}

	public static function admin_init()
	{
		// OAuth redirect handler
		self::add_action('load-zohowp_page_zohowp-oauth', 'process_authorization_code');
		// Register settings
		self::register_setting('zohowp_client_id', ['type' => 'string', 'default' => '']);
		self::register_setting('zohowp_client_secret', ['type' => 'string', 'default' => '']);
		self::register_setting('zohowp_refresh_token', ['type' => 'string', 'default' => '']);
		self::register_setting('zohowp_oauth_domain', ['type' => 'string', 'default' => 'https://accounts.zoho.com']);
		self::register_setting('zohowp_oauth_meta', ['type' => 'array', 'default' => ['code' => '', 'location' => 'us']]);
		// Add sections & fields
		self::add_section(
			'status',
			__('Connection Status', 'zoho-wp'),
			'status_section'
		);
		self::add_section(
			'client',
			__('OAuth Client', 'zoho-wp'),
			'client_section'
		);
		self::add_field(
			'client_id',
			__('Client ID', 'zoho-wp'),
			'client_id_field',
			'client'
		);
		self::add_field(
			'client_secret',
			__('Client Secret', 'zoho-wp'),
			'client_secret_field',
			'client'
		);
	}

	public static function process_authorization_code()
	{
		// Check for required parameters
		$code = filter_input(INPUT_GET, 'code');
		$location = filter_input(INPUT_GET, 'location');
		$page = filter_input(INPUT_GET, 'page');
		if (
			$page !== 'zohowp-oauth' ||
			empty($code) ||
			empty($location)
		)
			return;
		// Process code
		$result = \ZohoWP\API\OAuth::process_authorization_code($code);
		if ($result === false) {
			add_settings_error(
				'zohowp-oauth',
				'zohowp_error',
				__('Failed to connect your Zoho Account, please try again.', 'zoho-wp'),
				'error'
			);
			return false;
		}
		// Add success feedback
		add_settings_error(
			'zohowp-oauth',
			'zohowp_success',
			__('Your Zoho account has been successfully connected.', 'zoho-wp'),
			'success'
		);
		// Save meta
		update_option(
			'zohowp_oauth_meta',
			['code' => $code, 'location' => $location]
		);
		// Redirect without code parameters
		wp_redirect(get_admin_url() . 'admin.php?page=zohowp-oauth');
		exit;
	}

	public static function status_section()
	{
		$has_refresh = !empty(get_option('zohowp_refresh_token'));
		$has_client = !empty(get_option('zohowp_client_id')) && !empty('zohowp_client_secret');
?>
		<?php if ($has_refresh) : ?>
			<p><?php _e('You are currently connected to the Zoho API. To disconnect, delete the Client ID and Client Secret values below.', 'zoho-wp'); ?></p>
		<?php elseif ($has_client) : ?>
			<p><?php _e('Click the link below to connect your Zoho account.', 'zoho-wp'); ?></p>
			<a href='<?php echo \ZohoWP\API\OAuth::get_connection_link(); ?>' class='button button-primary'><?php _e('Connect to Zoho', 'zoho-wp'); ?></a>
		<?php else : ?>
			<p><?php _e('You are not connected to the Zoho API. Please follow the steps below to integrate your site with Zoho.', 'zoho-wp'); ?></p>
			<h3><?php _e('How to Connect', 'zoho-wp'); ?></h3>
			<ol>
				<li>
					<p>
						<?php _e('Create a server based application in the ', 'zoho-wp'); ?>
						<a href="https://api-console.zoho.com/"><?php _e('Zoho API Console', 'zoho-wp'); ?></a>.
					</p>
					<p>
						<b><?php _e('Use the following values when creating the application:', 'zoho-wp'); ?></b>
					<ul>
						<li><?php _e('Homepage URL: ', 'zoho-wp'); ?><code><?php echo get_site_url(); ?></code></li>
						<li><?php _e('Authorized Redirect URI: ', 'zoho-wp'); ?><code><?php echo \ZohoWP\API\OAuth::get_redirect_uri(); ?></code></li>
					</ul>
					</p>
					<p>
						<?php _e('View the ', 'zoho-wp'); ?>
						<a href="https://www.zoho.com/accounts/protocol/oauth-setup.html"><?php _e('Zoho OAuth Setup Guide', 'zoho-wp'); ?></a>
						<?php _e(' for more information.', 'zoho-wp'); ?>
					</p>
				</li>
				<li><?php _e('Enter the application&apos;s Client ID and Client Secret below.', 'zoho-wp'); ?></li>
				<li><?php _e('Save the values to continue to the next step.', 'zoho-wp'); ?></li>
			</ol>
		<?php endif; ?>
<?php
	}

	public static function client_section()
	{
	}

	public static function client_id_field()
	{
		self::render_field([
			'name' => 'zohowp_client_id',
			'value' => get_option('zohowp_client_id')
		]);
	}

	public static function client_secret_field()
	{
		self::render_field([
			'type' => 'password',
			'name' => 'zohowp_client_secret',
			'value' => get_option('zohowp_client_secret')
		]);
	}
}
