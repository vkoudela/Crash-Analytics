<?php namespace Koldy\Cache\Driver;
/**
 * If you don't want to use your cache driver, you can redirect all cache data
 * into black whole! Learn more at http://en.wikipedia.org/wiki//dev/null
 * 
 * This class handles the cache driver instance, but using it, nothing will happen.
 * This class will be initialized if you try to use driver that is disabled.
 * 
 */
class DevNull extends AbstractCacheDriver {

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::get()
	 */
	public function get($key) {
		$this->checkKey($key);
		return null;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::set()
	 */
	public function set($key, $value, $seconds = null) {
		$this->checkKey($key);
		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::add()
	 */
	public function add($key, $value, $seconds = null) {
		$this->checkKey($key);
		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::has()
	 */
	public function has($key) {
		$this->checkKey($key);
		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::delete()
	 */
	public function delete($key) {
		$this->checkKey($key);
		return null;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractDriver::deleteAll()
	 */
	public function deleteAll() {
		// nothing to delete
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractDriver::deleteOld()
	 */
	public function deleteOld($olderThen = null) {
		// nothing to delete
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::getOrSet()
	 */
	public function getOrSet($key, $functionOnSet, $seconds = null) {
		$this->checkKey($key);
		return call_user_func($functionOnSet);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::increment()
	 */
	public function increment($key, $howMuch = 1) {
		$this->checkKey($key);
		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::decrement()
	 */
	public function decrement($key, $howMuch = 1) {
		$this->checkKey($key);
		return true;
	}

}
