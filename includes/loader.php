<?php

namespace ZohoWP;

if (!defined('ABSPATH')) exit;

/**
 * Trait to simplify hooking static member functions
 */
trait Loader
{
	/**
	 * Wrapper for add_action
	 */
	final public static function add_action($hook, $callback, $priority = 10, $accepted_args = 1)
	{
		add_action($hook, [static::class, $callback], $priority, $accepted_args);
	}
	/**
	 * Wrapper for add_filter
	 */
	final public static function add_filter($hook, $callback, $priority = 10, $accepted_args = 1)
	{
		add_filter($hook, [static::class, $callback], $priority, $accepted_args);
	}
	/**
	 * Wrapper for add_action
	 */
	final public static function remove_action($hook, $callback, $priority = 10, $accepted_args = 1)
	{
		remove_action($hook, [static::class, $callback], $priority, $accepted_args);
	}
	/**
	 * Wrapper for add_filter
	 */
	final public static function remove_filter($hook, $callback, $priority = 10, $accepted_args = 1)
	{
		remove_filter($hook, [static::class, $callback], $priority, $accepted_args);
	}
}
