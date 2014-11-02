<?php namespace Koldy;

/**
 * Fetch the parameters passed via GET, POST, PUT or DELETE methods.
 * 
 * @link http://koldy.net/docs/input
 */
class Input {


	/**
	 * The raw data of the request
	 * 
	 * @var string
	 */
	private static $rawData = null;


	/**
	 * The variables in case of PUT or DELETE request
	 * 
	 * @var array
	 */
	private static $vars = null;


	/**
	 * Get raw data of the request
	 *
	 * @return mixed
	 */
	public static function getRawData() {
		if (static::$rawData === null) {
			static::$rawData = file_get_contents('php://input');
		}

		return static::$rawData;
	}


	/**
	 * Get the input vars
	 * 
	 * @return array
	 */
	private static function getInputVars() {
		if (static::$vars === null) {
			// take those vars only once
			parse_str(static::getRawData(), $vars);
			static::$vars = (array) $vars;
		}
		
		return static::$vars;
	}


	/**
	 * Fetch the value from the resource
	 * 
	 * @param string $resourceName
	 * @param string $name parameter name
	 * @param string $default [optional] default value if parameter doesn't exists
	 * @param array $allowed [optional] allowed values; if resource value doesn't contain one of values in this array, default is returned
	 * @return string
	 */
	private static function fetch($resourceName, $name, $default = null, array $allowed = null) {
		switch ($resourceName) {
			case 'GET':
				$resource = $_GET;
				break;

			case 'POST':
				if (!isset($_POST)) {
					return $default;
				}

				$resource = $_POST;
				break;

			case 'PUT':
				if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
					return $default;
				}
				
				$resource = static::getInputVars();
				break;

			case 'DELETE':
				if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
					return $default;
				}

				$resource = static::getInputVars();
				break;

			case 'REQUEST':
				$resource = $_REQUEST;
				break;
		}

		if (isset($resource[$name])) {
			if (is_array($resource[$name])) {
				return $resource[$name];
			}
			
			$value = trim($resource[$name]);

			if ($value === '') {
				return null;
			}

			if ($allowed !== null) {
				return (in_array($value, $allowed)) ? $value : $default;
			}

			return $value;
		} else {
			return $default;
		}
	}


	/**
	 * Does GET parameter exists or not
	 * 
	 * @param string $name
	 * @return boolean
	 * @link http://koldy.net/docs/input#get
	 */
	public static function hasGet($name) {
		return isset($_GET) && isset($_GET[$name]);
	}


	/**
	 * Returns the GET parameter
	 * 
	 * @param string $name [optional]
	 * @param string $default [optional]
	 * @param array $allowed [optional]
	 * @return string
	 * @link http://koldy.net/docs/input#get
	 */
	public static function get($name = null, $default = null, array $allowed = null) {
		if ($name === null) {
			return $_GET;
		}

		return self::fetch('GET', $name, $default, $allowed);
	}


	/**
	 * Does POST parameter exists or not
	 * 
	 * @param string $name
	 * @return boolean
	 * @link http://koldy.net/docs/input#post
	 */
	public static function hasPost($name) {
		return isset($_POST) && isset($_POST[$name]);
	}


	/**
	 * Returns the POST parameter
	 * 
	 * @param string $name
	 * @param string $default [optional] default NULL
	 * @param array $allowed [optional]
	 * @return string
	 * @link http://koldy.net/docs/input#post
	 */
	public static function post($name = null, $default = null, array $allowed = null) {
		if ($name === null) {
			return $_POST;
		}

		return self::fetch('POST', $name, $default, $allowed);
	}


	/**
	 * Does PUT parameter exists or not
	 * 
	 * @param string $name
	 * @return boolean
	 * @link http://koldy.net/docs/input#put
	 */
	public static function hasPut($name) {
		if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
			$variables = static::getInputVars();
			return isset($variables[$name]);
		} else {
			return false;
		}
	}


	/**
	 * Returns the PUT parameter
	 * 
	 * @param string $name [optional]
	 * @param string $default [optional]
	 * @param array $allowed [optional]
	 * @return string
	 * @link http://koldy.net/docs/input#put
	 */
	public static function put($name = null, $default = null, array $allowed = null) {
		if ($name === null && $_SERVER['REQUEST_METHOD'] == 'PUT') {
			return static::getInputVars();
		}

		return self::fetch('PUT', $name, $default, $allowed);
	}


	/**
	 * Does DELETE parameter exists or not
	 * 
	 * @param string $name
	 * @return boolean
	 * @link http://koldy.net/docs/input#delete
	 */
	public static function hasDelete($name) {
		if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
			$variables = static::getInputVars();
			return isset($variables[$name]);
		} else {
			return false;
		}
	}


	/**
	 * Returns the DELETE parameter
	 * 
	 * @param string $name [optional]
	 * @param string $default [optional]
	 * @param array $allowed [optional]
	 * @return string
	 * @link http://koldy.net/docs/input#delete
	 */
	public static function delete($name = null, $default = null, array $allowed = null) {
		if ($name === null && $_SERVER['REQUEST_METHOD'] == 'DELETE') {
			return static::getInputVars();
		}

		return self::fetch('DELETE', $name, $default, $allowed);
	}


	/**
	 * Returns the parameter from $_REQUEST
	 * 
	 * @param string $name [optional]
	 * @param string $default [optional]
	 * @param array $allowed [optional]
	 * @return string
	 */
	public static function getParam($name = null, $default = null, array $allowed = null) {
		if ($name === null) {
			return $_REQUEST;
		}

		return self::fetch('REQUEST', $name, $default, $allowed);
	}


	/**
	 * Get the required parameters. Return bad request if any of them is missing.
	 * 
	 * @param variable
	 * @return \stdClass
	 * @link http://koldy.net/docs/input#require
	 *
	 * @example
	 * 		$id = Input::requireParam('id');
	 * 		echo $id;
	 */
	public static function requireParam($name) {
		switch($_SERVER['REQUEST_METHOD']) {
			case 'GET': $parameters = $_GET; break;
			case 'POST': $parameters = $_POST; break;
			case 'PUT':
			case 'DELETE': $parameters = static::getInputVars(); break;
		}

		if (isset($parameters[$name])) {
			return $parameters[$name];
		} else {
			if (Application::inDevelopment()) {
				Log::debug("Missing {$name} parameter in {$_SERVER['REQUEST_METHOD']} request");
			}
			Application::error(400, 'Missing required parameter');
		}
	}


	/**
	 * Get the required parameters. Return bad request if any of them is missing.
	 * 
	 * @param variable
	 * @return \stdClass
	 * @link http://koldy.net/docs/input#require
	 *
	 * @example
	 * 		$params = Input::requireParams('id', 'email');
	 * 		echo $params->email;
	 */
	public static function requireParams() {
		switch($_SERVER['REQUEST_METHOD']) {
			case 'GET': $parameters = $_GET; break;
			case 'POST': $parameters = $_POST; break;
			case 'PUT':
			case 'DELETE': $parameters = static::getInputVars(); break;
		}

		$params = func_get_args();
		$class = new \stdClass;
		foreach ($params as $param) {
			if (!isset($parameters[$param])) {
				if (Application::inDevelopment()) {
					$passedParams = implode(',', array_keys($parameters));
					Log::debug("Missing {$_SERVER['REQUEST_METHOD']} parameter '{$param}', only got " . (strlen($passedParams) > 0 ? $passedParams : '[nothing]'));
				}
				Application::error(400, 'Missing one of the parameters');
			}

			$class->$param = $parameters[$param];
		}

		return $class;
	}


	/**
	 * Get the required parameters. Return bad request if any of them is missing. This method will return array.
	 * 
	 * @param variable
	 * @return array
	 * @link http://koldy.net/docs/input#require
	 *
	 * @example
	 * 		$params = Input::requireParamsArray('id', 'email');
	 * 		echo $params['email'];
	 */
	public static function requireParamsArray() {
		switch($_SERVER['REQUEST_METHOD']) {
			case 'GET': $parameters = $_GET; break;
			case 'POST': $parameters = $_POST; break;
			case 'PUT':
			case 'DELETE': $parameters = static::getInputVars(); break;
		}

		$params = func_get_args();
		$a = array();
		foreach ($params as $param) {
			if (!isset($parameters[$param])) {
				if (Application::inDevelopment()) {
					$passedParams = implode(',', array_keys($parameters));
					Log::debug("Missing {$_SERVER['REQUEST_METHOD']} parameter '{$param}', only got " . (strlen($passedParams) > 0 ? $passedParams : '[nothing]'));
				}
				Application::error(400, 'Missing one of the parameters');
			}

			$a[$param] = $parameters[$param];
		}

		return $a;
	}


	/**
	 * Get all parameters according to request method
	 * 
	 * @return array
	 * @link http://koldy.net/docs/input#all
	 */
	public static function all() {
		switch($_SERVER['REQUEST_METHOD']) {
			case 'GET':
				return $_GET;
				break;

			case 'POST':
				return $_POST;
				break;

			case 'PUT':
			case 'DELETE':
				return static::getInputVars();
				break;
		}
	}

}
