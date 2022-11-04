<?php

namespace ZohoWP;

require_once ZOHOWP_DIR_PATH . '/includes/trait-singleton.php';

class Zoho
{
	// TODO: These can all be static
	use Singleton;

	// Auth & setup

	protected function oauth_base_uri()
	{
		$options = get_option('zohowp');
		return empty($options['accounts_server'])
			? 'https://accounts.zoho.com'
			: $options['accounts_server'];
	}

	protected function campaigns_base_uri()
	{
		return 'https://campaigns.zoho.com/api/v1.1';
	}

	public function get_redirect_uri()
	{
		return get_admin_url() . 'admin.php?page=zohowp';
	}

	public function request_refresh_token_uri()
	{
		$options = get_option('zohowp');
		if (empty($options['client_id'])) return false;

		$base_uri = $this->oauth_base_uri();
		$query = http_build_query([
			'client_id' => $options['client_id'],
			'response_type' => 'code',
			'redirect_uri' => $this->get_redirect_uri(),
			'scope' => 'ZohoCampaigns.contact.ALL',
			'access_type' => 'offline',
			'prompt' => 'consent',
		]);
		return "$base_uri/oauth/v2/auth?$query";
	}

	public function request_access_token_uri($code)
	{
		$options = get_option('zohowp');
		if (empty($options['client_id']) || empty($options['client_secret'])) return false;

		$base_uri = $this->oauth_base_uri();
		$query = http_build_query([
			'client_id' => $options['client_id'],
			'grant_type' => 'authorization_code',
			'client_secret' => $options['client_secret'],
			'redirect_uri' => $this->get_redirect_uri(),
			'code' => $code,
		]);
		return "$base_uri/oauth/v2/token?$query";
	}

	public function refresh_access_token_uri()
	{
		$options = get_option('zohowp');
		if (empty($options['client_id']) || empty($options['client_secret']) || empty($options['refresh_token'])) return false;

		$base_uri = $this->oauth_base_uri();
		$query = http_build_query([
			'client_id' => $options['client_id'],
			'grant_type' => 'refresh_token',
			'client_secret' => $options['client_secret'],
			'refresh_token' => $options['refresh_token'],
		]);
		return "$base_uri/oauth/v2/token?$query";
	}

	public function update_token_information($body)
	{
		$json = json_decode($body, true);

		// Save access token in transient
		if (!empty($json['access_token']) && !empty($json['expires_in'])) {
			$access_token = $json['access_token'];
			$expires_in = $json['expires_in'];
			set_transient('zohowp_access_token', $access_token, $expires_in);
		}

		// Update options with token & info
		$updates = [
			'api_domain' => $json['api_domain'],
			'token_type' => $json['token_type'],
		];
		if (!empty($json['refresh_token'])) {
			$updates['refresh_token'] = $json['refresh_token'];
		}

		$options = get_option('zohowp');
		update_option('zohowp', array_merge($options, $updates), true);

		return $access_token;
	}

	public function merge_options($updates)
	{
		$options = get_option('zohowp');
		update_option('zohowp', array_merge($options, $updates), true);
	}

	public function get_access_token($allow_refetch = true)
	{
		$access_token = get_transient('zohowp_access_token');
		if ($access_token === false && $allow_refetch === true) {
			$uri = $this->refresh_access_token_uri();
			if ($uri === false) return false;
			$response = wp_remote_post($uri);
			if (is_wp_error($response)) return false;
			$body = wp_remote_retrieve_body($response);
			$access_token = $this->update_token_information($body);
		}
		return $access_token;
	}

	public function get_authorization_header($allow_refetch = true)
	{
		$access_token = $this->get_access_token($allow_refetch);
		$options = get_option('zohowp');
		$token_type = $options['token_type'];
		if (empty($token_type)) return false;
		return [
			'Authorization' => "$token_type $access_token"
		];
	}

	// Api Calls
	protected function api_request($method, $base_uri, $resource, $query_args)
	{
		// Get authorization header
		$auth = $this->get_authorization_header();
		if ($auth === false) return false;
		// Setup request
		$base_uri = $this->campaigns_base_uri();
		$query = http_build_query($query_args);
		$response = wp_remote_request(
			"$base_uri/$resource?$query",
			[
				'method' => $method,
				'headers' => $auth,
			]
		);
		// Error checking
		if (is_wp_error($response)) {
			var_dump($response);
			return false;
		}
		// Parse body and return
		$body = wp_remote_retrieve_body($response);
		return json_decode($body, true);
	}

	public function get_mailing_lists($ignore_cache = false)
	{
		// Check cache for response
		$items = get_transient('zohowp_cache_mailing_lists');
		if (!$ignore_cache && $items !== false) return $items;
		// Perform request
		$json = $this->api_request(
			'GET',
			$this->campaigns_base_uri(),
			'getmailinglists',
			['resfmt' => 'JSON']
		);
		$items = $json['list_of_details'];
		set_transient('zohowp_cache_mailing_lists', $items);
		return $items;
	}

	public function get_all_fields($ignore_cache = false)
	{
		// Check cache for response
		$items = get_transient('zohowp_cache_all_fields');
		if (!$ignore_cache && $items !== false) return $items;
		// Perform request
		$json = $this->api_request(
			'GET',
			$this->campaigns_base_uri(),
			'contact/allfields',
			['resfmt' => 'JSON']
		);
		$items = $json['response']['fieldnames']['fieldname'];
		set_transient('zohowp_cache_all_fields', $items);
		return $items;
	}

	public function subscribe($listkey, $contactinfo)
	{
		$json = $this->api_request(
			'POST',
			$this->campaigns_base_uri(),
			'listsubscribe',
			['resfmt' => 'JSON', 'listkey' => $listkey, 'contactinfo' => $contactinfo]
		);
		return $json;
	}
}
