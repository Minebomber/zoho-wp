<?php

namespace ZohoWP\API;

if (!defined('ABSPATH')) exit;

require_once ZOHOWP_DIR_PATH . '/includes/api/oauth.php';

class Base
{
	/**
	 * Get request headers containing the API access token
	 * @param boolean $allow_refetch Allows the token to be refreshed if it has expired
	 * @return false|array Array with an Authorization key or false if not available
	 */
	protected static function get_authorization_header($allow_refetch = true)
	{
		$access_token = OAuth::get_access_token($allow_refetch);
		if (empty($access_token)) return false;
		return [
			'Authorization' => "Zoho-oauthtoken $access_token"
		];
	}

	/**
	 * Perform a remote request to the Zoho API
	 * Includes an authorization header with the saved access token
	 * Performs a $method request to "$base_uri/$resource?$query"
	 * @param string $method Request method
	 * @param string $base_uri Base URI of the request
	 * @param string $resource Resource to fetch
	 * @param array $query_args Array of search params
	 */
	protected static function api_request($method, $base_uri, $resource, $query_args)
	{
		// Get authorization header
		$auth = self::get_authorization_header();
		if ($auth === false) return false;
		// Setup request
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

	private function __construct()
	{
	}
}
