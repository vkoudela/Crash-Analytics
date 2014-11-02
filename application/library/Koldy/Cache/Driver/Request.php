<?php namespace Koldy\Cache\Driver;
/**
 * This cache driver holds cached data only in request's scope. As soon as
 * request ends, everything will disappear
 *
 */
class Request extends AbstractCacheDriver {


	/**
	 * The array of loaded and/or data that will be stored
	 * 
	 * @var array
	 */
	private $data = array();


	/**
	 * Get the value from the cache by key
	 * 
	 * @param string $key
	 * @return mixed value or null if key doesn't exists or cache is disabled
	 */
	public function get($key) {
		if ($this->has($key)) {
			return $this->data[$key]->data;
		}

		return null;
	}

	/**
	 * Set the cache value by the key
	 * 
	 * @param string $key
	 * @param string $value
	 * @param integer $seconds
	 * @return boolean True if set, null if cache is disabled
	 */
	public function set($key, $value, $seconds = null) {
		$this->data[$key] = $object;
		return true;
	}

	/**
	 * The will add the value to the cache key only if it doesn't exists yet
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @param integer $seconds
	 * @return boolean True if set, false if it exists and null if cache is not enabled
	 */
	public function add($key, $value, $seconds = null) {
		if ($this->has($key)) {
			return false;
		}

		return $this->set($key, $value, $seconds);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::has()
	 */
	public function has($key) {
		return array_key_exists($key, $this->data);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::delete()
	 */
	public function delete($key) {
		if ($this->has($key)) {
			unset($this->data[$key]);
		}
		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractDriver::deleteAll()
	 */
	public function deleteAll() {
		$this->data = array();
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractDriver::deleteOld()
	 */
	public function deleteOld($olderThenSeconds = null) {
		// nothing to do
	}

}
