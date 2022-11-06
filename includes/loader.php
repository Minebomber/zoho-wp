<?php

namespace ZohoWP;

if (!defined('ABSPATH')) exit;

/**
 * Trait to simplify hooking static member functions
 */
trait Loader
{
	final public static function add_action($hook, $callback, $priority = 10, $accepted_args = 1)
	{
		add_action($hook, [static::class, $callback], $priority, $accepted_args);
	}
	final public static function add_filter($hook, $callback, $priority = 10, $accepted_args = 1)
	{
		add_filter($hook, [static::class, $callback], $priority, $accepted_args);
	}
}
