<?php

namespace ZohoWP\API;

if (!defined('ABSPATH')) exit;

class OAuth
{
	/**
	 * Get the Zoho redirect URI for this site
	 */
	public static function get_redirect_uri()
	{
		return get_admin_url() . 'admin.php?page=zohowp-oauth';
	}

	/**
	 * URI that user visits to connect account (request refresh token)
	 */
	public static function get_connection_link()
	{
		$client_id = get_option('zohowp_client_id', '');
		if (empty($client_id)) return false;

		$base_uri = self::oauth_base_uri();
		$query = http_build_query([
			'client_id' => $client_id,
			'response_type' => 'code',
			'redirect_uri' => self::get_redirect_uri(),
			'scope' => 'ZohoCampaigns.contact.ALL',
			'access_type' => 'offline',
			'prompt' => 'consent',
		]);
		return "$base_uri/oauth/v2/auth?$query";
	}

	/**
	 * Request API tokens with OAuth code and save them in options
	 * @param string $code The authorization code returned by Zoho OAuth
	 */
	public static function process_authorization_code($code)
	{
		$response = wp_remote_post(self::request_access_token_uri($code));
		if (is_wp_error($response))
			return false;
		$body = wp_remote_retrieve_body($response);
		return self::process_token_update($body);
	}

	/**
	 * Retrieve the Zoho API access token for the current connection
	 * @param boolean $allow_refetch Allows the token to be refreshed if it has expired
	 * @return false|string Access token or false if not available
	 */
	public static function get_access_token($allow_refetch = true)
	{
		$access_token = get_transient('zohowp_access_token');
		if ($access_token === false && $allow_refetch === true) {
			$uri = self::refresh_access_token_uri();
			if ($uri === false) return false;
			$response = wp_remote_post($uri);
			if (is_wp_error($response)) return false;
			$body = wp_remote_retrieve_body($response);
			$access_token = self::process_token_update($body);
		}
		return $access_token;
	}

	/**
	 * Get the base uri for Zoho OAuth requests
	 */
	protected static function oauth_base_uri()
	{
		$cached = get_option('zohowp_oauth_domain');
		if (!empty($cached)) return $cached;
		return 'https://accounts.zoho.com';
	}

	/**
	 * URI to request Zoho API tokens from an OAuth redirect response
	 * @param string $code The authorization code returned by Zoho OAuth
	 */
	protected static function request_access_token_uri($code)
	{
		$client_id = get_option('zohowp_client_id', '');
		$client_secret = get_option('zohowp_client_secret', '');
		if (empty($client_id) || empty($client_secret)) return false;
		$base_uri = self::oauth_base_uri();
		$query = http_build_query([
			'client_id' => $client_id,
			'grant_type' => 'authorization_code',
			'client_secret' => $client_secret,
			'redirect_uri' => self::get_redirect_uri(),
			'code' => $code,
		]);
		return "$base_uri/oauth/v2/token?$query";
	}

	/**
	 * URI to refresh the access token using the saved refresh token
	 */
	protected static function refresh_access_token_uri()
	{
		$client_id = get_option('zohowp_client_id', '');
		$client_secret = get_option('zohowp_client_secret', '');
		$refresh_token = get_option('zohowp_refresh_token', '');
		if (empty($client_id) || empty($client_secret) || empty($refresh_token)) return false;
		$base_uri = self::oauth_base_uri();
		$query = http_build_query([
			'client_id' => $client_id,
			'grant_type' => 'refresh_token',
			'client_secret' => $client_secret,
			'refresh_token' => $refresh_token,
		]);
		return "$base_uri/oauth/v2/token?$query";
	}

	/**
	 * Update saved token options from data in remote response
	 * @param string $body Response body (JSON)
	 */
	protected static function process_token_update($body)
	{
		$json = json_decode($body, true);
		// Save access token in transient
		if (!empty($json['access_token']) && !empty($json['expires_in'])) {
			$access_token = $json['access_token'];
			$expires_in = $json['expires_in'];
			set_transient('zohowp_access_token', $access_token, $expires_in);
		}
		// Check error
		if (!empty($json['error'])) {
			error_log('ZohoWP OAuth Error: ' . $json['error']);
			return false;
		}
		if (!empty($json['refresh_token'])) {
			update_option('zohowp_refresh_token', $json['refresh_token']);
		}
		return $access_token;
	}

	private function __construct()
	{
	}
}
