<?php
namespace ZohoWP\API;

if (!defined('ABSPATH')) exit;

require_once ZOHOWP_DIR_PATH . '/includes/api/base.php';

class Campaigns extends Base
{
	/**
 	 * Get all Zoho mailing lists
	 * @param boolean $ignore_cache Ignore any cached response and fetch fresh data
	 */
	public static function get_mailing_lists($ignore_cache = false)
	{
		// Check cache for response
		$items = get_transient('zohowp_cache_mailing_lists');
		if (!$ignore_cache && $items !== false) return $items;
		// Perform request
		$json = self::api_request(
			'GET',
			self::campaigns_base_uri(),
			'getmailinglists',
			['resfmt' => 'JSON']
		);
		$items = $json['list_of_details'];
		set_transient('zohowp_cache_mailing_lists', $items);
		return $items;
	}

	/**
 	 * Get the Zoho user schema
	 * @param boolean $ignore_cache Ignore any cached response and fetch fresh data
	 */
	public static function get_all_fields($ignore_cache = false)
	{
		// Check cache for response
		$items = get_transient('zohowp_cache_all_fields');
		if (!$ignore_cache && $items !== false) return $items;
		// Perform request
		$json = self::api_request(
			'GET',
			self::campaigns_base_uri(),
			'contact/allfields',
			['resfmt' => 'JSON']
		);
		$items = $json['response']['fieldnames']['fieldname'];
		set_transient('zohowp_cache_all_fields', $items);
		return $items;
	}

	/**
 	 * Subscribe a user to a mailing list
	 * @param string $listkey The Zoho mailing list key
	 * @param array $contactinfo The user info to add
	 */
	public static function subscribe($listkey, $contactinfo)
	{
		$json = self::api_request(
			'POST',
			self::campaigns_base_uri(),
			'json/listsubscribe',
			['resfmt' => 'JSON', 'listkey' => $listkey, 'contactinfo' => json_encode($contactinfo)]
		);
		return $json;
	}

	/**
	 * Get the base uri for Zoho Campaigns requests
	 */
	protected static function campaigns_base_uri()
	{
		return 'https://campaigns.zoho.com/api/v1.1';
	}
}
