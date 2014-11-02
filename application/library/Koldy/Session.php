<?php namespace Koldy;

/**
 * The session class. Its easy to use, just make sure that your configuration
 * is valid. Everything else will be just straight forward.
 *
 */
class Session {


	/**
	 * Flag if session has been initialized or not
	 * 
	 * @var boolean
	 */
	private static $initialized = false;


	/**
	 * Flag if session has been write closed
	 * 
	 * @var boolean
	 */
	private static $closed = false;


	/**
	 * Initialize the session handler and session itself
	 * 
	 * @throws Exception
	 */
	private static function init() {
		if (!static::$initialized) {
			static::$initialized = true;

			$config = Application::getConfig('session');

			if ($config === null) {
				throw new Exception('Missing config file for sessions');
			}

			session_set_cookie_params(
				$config['cookie_life'],
				$config['cookie_path'],
				$config['cookie_domain'],
				$config['cookie_secure']
			);

			session_name($config['session_name']);

			if (isset($config['driver'])) {
				if (in_array($config['driver'], array('\Koldy\Session\Driver\File', '\Koldy\Session\Driver\Db')) && PHP_VERSION_ID < 50400) {
					throw new Exception('PHP 5.4 or greater is reqired to use this session driver: ' . $config['driver']);
				}

				$driverClass = $config['driver'];
				$handler = new $driverClass(isset($config['options']) ? $config['options'] : array());

				if (!($handler instanceof \SessionHandlerInterface)) {
					throw new Exception('Your session driver doesn\'t implement the \SessionHandlerInterface');
				}

				session_set_save_handler($handler);
			}

			session_start();
		}
	}


	/**
	 * Get the value from session under given key name
	 * 
	 * @param string $key
	 * @return mixed|null
	 * @link http://koldy.net/docs/session#get
	 */
	public static function get($key) {
		static::init();

		if (is_object($key) || is_array($key)) {
			throw new \InvalidArgumentException('The key mustn\'t be array or class instance');
		}

		return array_key_exists($key, $_SESSION)
			? $_SESSION[$key]
			: null;
	}


	/**
	 * Set the key to the session. If key already exists, it will be overwritten
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @throws Exception
	 * @throws \InvalidArgumentException
	 * @link http://koldy.net/docs/session#set
	 */
	public static function set($key, $value) {
		if (static::$closed) {
			throw new Exception('Can not set any other value to session because all data has been already committed');
		}

		static::init();
		
		if (is_object($key) || is_array($key)) {
			throw new \InvalidArgumentException('The key mustn\'t be array or class instance');
		}

		$_SESSION[$key] = $value;
	}


	/**
	 * Add the key to session but only if that key doesn't already exist
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @throws Exception
	 * @throws \InvalidArgumentException
	 * @link http://koldy.net/docs/session#add
	 */
	public static function add($key, $value) {
		if (static::$closed) {
			throw new Exception('Can not set any other value to session because all data has been already committed');
		}

		if (is_object($key) || is_array($key)) {
			throw new \InvalidArgumentException('The key mustn\'t be array or class instance');
		}

		static::init();

		if (!array_key_exists($key, $_SESSION)) {
			$_SESSION[$key] = $value;
		}
	}


	/**
	 * Does given key exists in session or not?
	 * 
	 * @param string $key
	 * @return boolean
	 * @throws \InvalidArgumentException
	 * @link http://koldy.net/docs/session#has
	 */
	public static function has($key) {
		static::init();

		if (is_object($key) || is_array($key)) {
			throw new \InvalidArgumentException('The key mustn\'t be array or class instance');
		}

		return array_key_exists($key, $_SESSION);
	}


	/**
	 * Delete/remove the key from the session data
	 * 
	 * @param string $key
	 * @throws \InvalidArgumentException
	 * @link http://koldy.net/docs/session#delete
	 */
	public static function delete($key) {
		static::init();

		if (is_object($key) || is_array($key)) {
			throw new \InvalidArgumentException('The key mustn\'t be array or class instance');
		}

		if (array_key_exists($key, $_SESSION)) {
			unset($_SESSION[$key]);
		}
	}


	/**
	 * Get or set the key into session. If key already exists in session, then
	 * its value will be returned, otherwise, function will be called, key
	 * will be set with the value returned from function and that value will be
	 * returned to you
	 * 
	 * @param string $key
	 * @param function $functionOnSet
	 * @throws Exception
	 * @throws \InvalidArgumentException
	 * @return mixed
	 * @link http://koldy.net/docs/session#getOrSet
	 */
	public static function getOrSet($key, $functionOnSet) {
		static::init();

		if (is_object($key) || is_array($key)) {
			throw new \InvalidArgumentException('The key mustn\'t be array or class instance');
		}

		if (!array_key_exists($key, $_SESSION)) {
			if (static::$closed) {
				throw new Exception('Can not set any other value to session because all data has been already committed');
			}

			if (!($functionOnSet instanceof \Closure)) {
				throw new \InvalidArgumentException('Second parameter in Session::getOrSet must be the instance of PHP\'s Closure');
			}

			$_SESSION[$key] = call_user_func($functionOnSet);
		}

		return $_SESSION[$key];
	}


	/**
	 * Call session_write_close(). Usually, that function is called internally by
	 * PHP on request execution end, but you can also call it by yourself. But
	 * be CAREFUL! Once you call this method, you can not add or set any new
	 * data to session!
	 * 
	 * @link @link http://koldy.net/docs/session#close
	 */
	public static function close() {
		static::init();
		session_write_close();
		static::$closed = true;
	}


	/**
	 * Is session write closed or not?
	 * 
	 * @return boolean
	 * @link http://koldy.net/docs/session#close
	 */
	public static function isClosed() {
		return static::$closed;
	}


	/**
	 * You can start session with this method if you need that. Session start
	 * will be automatically called with any of other static methods (excluding
	 * hasStarted() method)
	 * 
	 * @link http://koldy.net/docs/session#start
	 */
	public static function start() {
		static::init();
	}


	/**
	 * Is session already started or not?
	 * 
	 * @return boolean
	 * @link http://koldy.net/docs/session#start
	 */
	public static function hasStarted() {
		return static::$initialized;
	}


	/**
	 * Destroy session completely
	 * 
	 * @link http://koldy.net/docs/session#destroy
	 */
	public static function destroy() {
		static::init();
		session_unset();
		session_destroy();
	}

}
