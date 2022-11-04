<?php

namespace ZohoWP;

/**
 * Singleton trait to implements Singleton pattern in any classes where this trait is used.
 */
trait Singleton
{
	protected static $_instance = array();

	/**
	 * Protected class constructor to prevent direct object creation.
	 */
	protected function __construct()
	{
	}

	/**
	 * Prevent object cloning
	 */
	final protected function __clone()
	{
	}

	/**
	 * To return new or existing Singleton instance of the class from which it is called.
	 * As it sets to final it can't be overridden.
	 *
	 * @return object Singleton instance of the class.
	 */
	final public static function instance()
	{
		$class = get_called_class();
		if (!isset(static::$_instance[$class])) {
			static::$_instance[$class] = new $class();
		}
		return static::$_instance[$class];
	}
}
