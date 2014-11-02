<?php namespace Koldy;

/**
 * The cache class.
 * 
 * @link http://koldy.net/docs/cache
 */
class Cache {


	/**
	 * The initialized drivers
	 * 
	 * @var array
	 */
	protected static $drivers = null;


	/**
	 * The default driver key (the first key from cache array)
	 * 
	 * @var string
	 */
	protected static $defaultDriver = null;


	/**
	 * Initialize the cache mechanizm
	 */
	protected static function init() {
		if (static::$drivers === null) {
			static::$drivers = array();
			$config = Application::getConfig('cache');
			$default = array_keys($config);

			if (!isset($default[0])) {
				throw new Exception('You\'re trying to use cache without any driver defined in cache config!');
			}

			static::$defaultDriver = $default[0];
		}
	}


	/**
	 * Get the cache driver
	 * 
	 * @param string $driver [optional]
	 * @return \Koldy\Cache\Driver\AbstractCacheDriver
	 * @throws \Koldy\Exception
	 */
	protected static function getDriver($driver = null) {
		static::init();
		if ($driver === null) {
			if (static::$defaultDriver === null) {
				throw new Exception('You\'re trying to use cache without any driver defined in cache config!');
			} else {
				$driver = static::$defaultDriver;
			}
		}

		if (isset(static::$drivers[$driver])) {
			return static::$drivers[$driver];
		}

		$config = Application::getConfig('cache');
		if (!isset(static::$drivers[$driver])) {
			if (!isset($config[$driver])) {
				throw new Exception("Cache driver '{$driver}' is not defined in config");
			}

			if (!$config[$driver]['enabled']) {
				static::$drivers[$driver] = new Cache\Driver\DevNull(array());
			} else {
				$constructor = (isset($config[$driver]['options'])) ? $config[$driver]['options'] : array();
				$className = $config[$driver]['driver_class'];

				if (!class_exists($className, true)) {
					throw new Exception("Unknown cache class={$className} under key={$driver}");
				}

				static::$drivers[$driver] = new $className($constructor);
			}

		} else if (!$config[$driver]['enabled']) {
			static::$drivers[$driver] = new Cache\Driver\DevNull(array());
		}

		return static::$drivers[$driver];
	}


	/**
	 * Get the key from default cache driver
	 * 
	 * @param string $key
	 * @return mixed
	 * @link http://koldy.net/docs/cache#get
	 */
	public static function get($key) {
		return static::getDriver()->get($key);
	}


	/**
	 * Set the value to default cache no matter does this key already exists or not
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @param int $seconds
	 * @return true if set
	 * @link http://koldy.net/docs/cache#set
	 */
	public static function set($key, $value, $seconds = null) {
		return static::getDriver()->set($key, $value, $seconds);
	}


	/**
	 * Add the key to the cache only if that key doesn't already exists
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @param int $seconds
	 * @return true if set, false otherwise
	 * @link http://koldy.net/docs/cache#add
	 */
	public static function add($key, $value, $seconds = null) {
		return static::getDriver()->add($key, $value, $seconds);
	}


	/**
	 * Is there a key under default cache
	 * 
	 * @param string $key
	 * @return boolean
	 * @link http://koldy.net/docs/cache#has
	 */
	public static function has($key) {
		return static::getDriver()->has($key);
	}


	/**
	 * Delete the key from cache
	 * 
	 * @param string $key
	 * @return boolean
	 * @link http://koldy.net/docs/cache#delete
	 */
	public static function delete($key) {
		return static::getDriver()->delete($key);
	}


	/**
	 * Get or set the key's value
	 * 
	 * @param string $key
	 * @param function $functionOnSet
	 * @param int $seconds
	 * @link http://koldy.net/docs/cache#get-or-set
	 */
	public static function getOrSet($key, $functionOnSet, $seconds = null) {
		return static::getDriver()->getOrSet($key, $functionOnSet, $seconds);
	}


	/**
	 * Increment value in cache
	 * 
	 * @param string $key
	 * @param int $howMuch
	 * @return bool was it incremented or not
	 * @link http://koldy.net/docs/cache#increment-decrement
	 */
	public static function increment($key, $howMuch = 1) {
		return static::getDriver()->increment($key, $howMuch);
	}


	/**
	 * Decrement value in cache
	 * 
	 * @param string $key
	 * @param number $howMuch
	 * @return bool was it decremented or not
	 * @link http://koldy.net/docs/cache#increment-decrement
	 */
	public static function decrement($key, $howMuch = 1) {
		return static::getDriver()->decrement($key, $howMuch);
	}


	/**
	 * Get the cache driver that isn't default
	 * 
	 * @param string $driver
	 * @return \Koldy\Cache\Driver\AbstractCacheDriver
	 * @link http://koldy.net/docs/cache#engines
	 */
	public static function driver($driver) {
		return static::getDriver($driver);
	}


	/**
	 * Does given driver exists (this will also return true if driver is disabled)
	 * 
	 * @param string $driver
	 * @return boolean
	 * @link http://koldy.net/docs/cache#engines
	 */
	public static function hasDriver($driver) {
		return (Application::getConfig('cache', $driver) !== null);
	}


	/**
	 * Is given cache driver enabled or not? If driver is instance of
	 * DevNull, it will also return false so be careful about that
	 * 
	 * @param string $driver
	 * @return boolean
	 */
	public static function isEnabled($driver = null) {
		return !(static::getDriver($driver) instanceof Cache\Driver\DevNull);
	}

}
