<?php

namespace ZohoWP;

require_once ZOHOWP_DIR_PATH . '/includes/trait-singleton.php';
require_once ZOHOWP_DIR_PATH . '/includes/class-zoho.php';

class Admin
{
	use Singleton;

	public function add_admin_menu()
	{
		add_menu_page(
			__('ZohoWP', 'zoho-wp'),
			__('ZohoWP', 'zoho-wp'),
			'administrator',
			'zohowp',
			[$this, 'render_page'],
			'',
			2
		);
	}

	public function render_page()
	{
		// Handle oauth redirect
		if (!empty($_GET['code']) && !empty($_GET['location']) && !empty($_GET['accounts-server'])) {
			$code = $_GET['code'];
			$location = $_GET['location'];
			$accounts_server = $_GET['accounts-server'];
			$zoho = Zoho::instance();
			$zoho->merge_options(['location' => $location, 'accounts_server' => $accounts_server]);
			$response = wp_remote_post($zoho->request_access_token_uri($code));
			if (!is_wp_error($response)) {
				$body = wp_remote_retrieve_body($response);
				$zoho->update_token_information($body);
				// Redirect back to page to clear out the parameters
?>
				<script>
					jQuery(document).ready(function() {
						window.location.replace('<?php echo $zoho->get_redirect_uri(); ?>');
					});
				</script>
		<?php
			}
		}
		?>
		<div class='wrap'>
			<h2><?php _e('Zoho for Wordpress Settings', 'zoho-wp'); ?></h2>
			<?php settings_errors('zohowp'); ?>
			<form method='post' action='options.php'>
				<?php
				settings_fields('zohowp');
				do_settings_sections('zohowp');
				submit_button();
				?>
			</form>
		</div>
	<?php
	}

	public function register_settings()
	{
		add_settings_section(
			'zohowp',
			'',
			[$this, 'render_section'],
			'zohowp'
		);
		add_settings_field(
			'zohowp_client_id',
			'Client ID',
			[$this, 'render_field_client_id'],
			'zohowp',
			'zohowp'
		);
		add_settings_field(
			'zohowp_client_secret',
			'Client Secret',
			[$this, 'render_field_client_secret'],
			'zohowp',
			'zohowp'
		);

		register_setting(
			'zohowp',
			'zohowp',
			[
				'type' => 'array',
				'sanitize_callback' => [$this, 'sanitize_settings'],
				'default' => [
					'client_id' => '',
					'client_secret' => ''
				]
			]
		);
	}

	public function render_section()
	{
		$connection = get_option('zohowp');
		// Connection info
		if (!empty($connection['refresh_token'])) {
			$status = 'connected';
			$status_text = __('Connected', 'zoho-wp');
		} elseif (!empty($connection['client_id']) && !empty($connection['client_secret'])) {
			$status = 'ready';
			$status_text = __('Ready', 'zoho-wp');
			$connect_uri = Zoho::instance()->request_refresh_token_uri($connection['client_id']);
		} else {
			$status = 'incomplete';
			$status_text = __('Incomplete', 'zoho-wp');
		}
	?>
		<h3><?php echo sprintf(__('Connection Status: %s', 'zoho-wp'), $status_text); ?></h3>

		<?php if ($status === 'connected') : ?>
			<p><?php _e('To disconnect, delete the Client ID and Client Secret values below', 'zoho-wp'); ?></p>
		<?php elseif ($status === 'ready') : ?>
			<a href='<?php echo $connect_uri; ?>' class='button button-primary'><?php _e('Connect to Zoho', 'zoho-wp'); ?></a>
		<?php elseif ($status === 'incomplete') : ?>
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
						<li><?php _e('Authorized Redirect URI: ', 'zoho-wp'); ?><code><?php echo Zoho::instance()->get_redirect_uri(); ?></code></li>
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

	public function render_field_client_id()
	{
		$name = 'zohowp[client_id]';
		$value = get_option('zohowp')['client_id'] ?? '';
	?>
		<input type='text' autocomplete='off' name='<?php echo $name; ?>' value='<?php echo $value; ?>' />
	<?php
	}

	public function render_field_client_secret()
	{
		$name = 'zohowp[client_secret]';
		$value = get_option('zohowp')['client_secret'] ?? '';
	?>
		<input type='password' autocomplete='off' name='<?php echo $name; ?>' value='<?php echo $value; ?>' />
<?php
	}

	public function sanitize_settings($input)
	{
		$options = get_option('zohowp');
		$clean =  array_map('trim', $input);
		// If ID/secret changes, tokens are invalid
		if ($input['client_id'] !== $options['client_id'] || $input['client_secret'] !== $options['client_secret']) {
			delete_transient('zohowp_access_token');
			delete_transient('zohowp_cache_mailing_lists');
			delete_transient('zohowp_cache_all_fields');
			return $clean;
		}
		return array_merge($options, $clean);
	}
}
