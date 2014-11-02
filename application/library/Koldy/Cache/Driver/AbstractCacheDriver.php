<?php namespace Koldy\Cache\Driver;

/**
 * Abstract class for making any kind of new cache driver.
 * 
 * @link http://koldy.net/docs/cache#custom
 */
abstract class AbstractCacheDriver {


	/**
	 * Array of loaded config (the options part)
	 * 
	 * @var array
	 */
	protected $config = null;


	/**
	 * Default duration of cache - if user doesn't pass it to set/add methods
	 * 
	 * @var int
	 */
	protected $defaultDuration = 3600;


	/**
	 * Construct the object by array of config properties. Config keys are set
	 * in config/cache.php and this array will contain only block for the
	 * requested cache driver. Yes, you can also build this manually, but that
	 * is not recommended.
	 * 
	 * @param array $config
	 */
	public function __construct(array $config) {
		if (isset($config['default_duration']) && (int) $config['default_duration'] > 0) {
			$this->defaultDuration = (int) $config['default_duration'];
		}

		$this->config = $config;

		if (isset($config['clean_old']) && $config['clean_old'] === true) {
			$self = $this;
			register_shutdown_function(function() use($self) {
				$self->deleteOld();
			});
		}
	}


	/**
	 * Validate key name and throw exception if something is wrong
	 * 
	 * @param string $key
	 * @throws \InvalidArgumentException
	 */
	protected function checkKey($key) {
		// the max length is 255-32-1 = 222
		if (!is_string($key) || strlen($key) > 222) {
			throw new \InvalidArgumentException(
				!is_string($key)
				? ('Passed cache key name must be string; ' . gettype($key) . ' given')
				: 'Cache key name mustn\'t be longer then 222 characters'
			);
		}
	}


	/**
	 * Get the value from cache by given key
	 * 
	 * @param string $key
	 * @return mixed value or null if key doesn't exists or cache is disabled
	 * @link http://koldy.net/docs/cache#get
	 */
	abstract public function get($key);


	/**
	 * Set the value to cache identified by key
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @param string $seconds [optional] if not set, default is used
	 * @return boolean True if set, null if cache is disabled
	 * @link http://koldy.net/docs/cache#set
	 */
	abstract public function set($key, $value, $seconds = null);


	/**
	 * Set the value under key and remember it forever! Okay, "forever" has its
	 * own duration and thats for 15 years. So, is 15 years enough for you?
	 * 
	 * @param string $key
	 * @param mixes $value
	 * @return boolean True if set, null if cache is disabled
	 */
	public function setForever($key, $value) {
		$this->checkKey($key);

		return $this->set($key, $value, time() + 3600 * 24 * 365 * 15);
	}


	/**
	 * Add value to cache only if value wasn't cached before (or has expired)
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @param string $seconds
	 * @return boolean True if set, false if it exists and null if cache is not enabled
	 * @link http://koldy.net/docs/cache#add
	 */
	public function add($key, $value, $seconds = null) {
		$this->checkKey($key);

		if ($this->has($key)) {
			return false;
		}
		
		return $this->set($key, $value, $seconds);
	}


	/**
	 * Check if item under key name exists. It will return false if item expired.
	 * 
	 * @param string $key
	 * @return boolean
	 * @link http://koldy.net/docs/cache#has
	 */
	abstract public function has($key);


	/**
	 * Delete the item from cache
	 * 
	 * @param string $key
	 * @return boolean True if file is deleted, False if not, null if there is nothing to delete
	 * @link http://koldy.net/docs/cache#delete
	 */
	abstract public function delete($key);


	/**
	 * Delete all cached items
	 */
	abstract public function deleteAll();


	/**
	 * Delete all cache items older then ...
	 * 
	 * @param int $olderThen [optional] if not set, then default duration is used
	 */
	abstract public function deleteOld($olderThen = null);


	/**
	 * Get the value from cache if exists, otherwise, set the value returned
	 * from the function you pass. The function may contain more steps, such as
	 * fetching data from database or etc.
	 * 
	 * @param string $key
	 * @param function $functionOnSet
	 * @param integer $seconds
	 * @return mixed
	 * 
	 * @example
	 * Cache::getOrSet('key', function() {
	 * 	return "the value";
	 * });
	 */
	public function getOrSet($key, $functionOnSet, $seconds = null) {
		$this->checkKey($key);

		if ($this->has($key)) {
			return $this->get($key);
		} else {
			$value = call_user_func($functionOnSet);
			$this->set($key, $value, $seconds);
			return $value;
		}
	}


	/**
	 * Increment number value in cache. This will not work if item expired!
	 * 
	 * @param string $key
	 * @param int $howMuch [optional] default 1
	 * @return boolean was it incremented or not
	 * @link http://koldy.net/docs/cache#increment-decrement
	 */
	public function increment($key, $howMuch = 1) {
		$this->checkKey($key);

		$data = $this->get($key);
		if ($data !== null) {
			$data += $howMuch;
			$this->set($key, $data);
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Decrement number value in cache. This will not work if item expired!
	 * 
	 * @param string $key
	 * @param int $howMuch [optional] default 1
	 * @return boolean was it incremented or not
	 * @link http://koldy.net/docs/cache#increment-decrement
	 */
	public function decrement($key, $howMuch = 1) {
		$this->checkKey($key);

		$data = $this->get($key);
		if ($data !== null) {
			$data -= $howMuch;
			$this->set($key, $data);
			return true;
		} else {
			return false;
		}
	}

}
