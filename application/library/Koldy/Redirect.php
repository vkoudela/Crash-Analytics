<?php namespace Koldy;

/**
 * Perform redirect be flushing redirect headers to client. Usually, you'll use
 * this class as return value from method in your controller classes.
 * 
 * @example
 * 
 * 		class PageController {
 * 			public function userAction() {
 * 				return Redirect::href('user', 'list');
 * 			}
 * 		}
 * 
 * @link http://koldy.net/docs/redirect
 *
 */
class Redirect extends Response {


	/**
	 * Permanent redirect (301) to the given URL
	 * 
	 * @param string $where
	 * @return \Koldy\Redirect
	 * @link http://koldy.net/docs/redirect#methods
	 */
	public static function permanent($where) {
		$self = new static();
		$self
			->parentHeader('Location', $where)
			->httpHeader(301, 'HTTP/1.1 301 Moved Permanently')
			->header('Status', '301 Moved Permanently')
			->header('Connection', 'close')
			->header('Content-Length', 0);

		return $self;
	}


	/**
	 * Temporary redirect (302) to the given URL
	 * 
	 * @param string $where
	 * @return \Koldy\Redirect
	 * @link http://koldy.net/docs/redirect#methods
	 */
	public static function temporary($where) {
		$self = new static();
		$self
			->parentHeader('Location', $where)
			->httpHeader(302, 'HTTP/1.1 302 Moved Temporary')
			->header('Status', '302 Moved Temporary')
			->header('Connection', 'close')
			->header('Content-Length', 0);

		return $self;
	}


	/**
	 * Alias to temporary() method
	 * 
	 * @param string $where
	 * @return \Koldy\Redirect
	 * @example http://www.google.com
	 * @link http://koldy.net/docs/redirect#methods
	 */
	public static function to($where) {
		return static::temporary($where);
	}


	/**
	 * Redirect user to home page
	 * 
	 * @return \Koldy\Redirect
	 * @link http://koldy.net/docs/redirect#usage
	 */
	public static function home() {
		return static::href();
	}


	/**
	 * Redirect user to the URL generated with Url::href
	 * 
	 * @param string $controller [optional]
	 * @param string $action [optional]
	 * @param array $params [optional]
	 * @return \Koldy\Redirect
	 * @link http://koldy.net/docs/redirect#usage
	 * @link http://koldy.net/docs/url#href
	 */
	public static function href($controller = null, $action = null, array $params = null) {
		return static::temporary(Application::route()->href($controller, $action, $params));
	}


	/**
	 * Redirect user the the given link under the same domain.
	 * 
	 * @param string $path
	 * @return \Koldy\Redirect
	 * @link http://koldy.net/docs/redirect#usage
	 * @link http://koldy.net/docs/url#link
	 */
	public static function link($path) {
		return self::temporary(Application::route()->link($path));
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Response::header($name, $value)
	 */
	public function header($name, $value = null) {
		if ($name == 'Location') {
			throw new Exception('Using \'Location\' for header name is not permitted');
		}
	
		return parent::header($name, $value);
	}

	/**
	 * Bypass location check
	 * 
	 * @param string $name
	 * @param string $value
	 * @return \Koldy\Response
	 */
	private function parentHeader($name, $value = null) {
		return parent::header($name, $value);
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Response::flush()
	 */
	public function flush() {
		$this->flushHeaders();
		flush();
		
		if (function_exists('fastcgi_finish_request')) {
			@fastcgi_finish_request();
		}

		if ($this->workAfterResponse !== null) {
			call_user_func($this->workAfterResponse);
		}
	}

}
