<?php namespace Koldy\Http;

use Koldy\Exception;
use Koldy\Directory;

/**
 * Make HTTP request to any given URL.
 * This class requires PHP CURL extension!
 * @TODO završiti ovo
 *
 */
class Request {
	
	const GET = 'GET';
	const POST = 'POST';


	/**
	 * @var string
	 */
	protected $url = null;


	/**
	 * @var array
	 */
	protected $params = array();


	/**
	 * @var string
	 */
	protected $type = 'GET';


	/**
	 * The CURL options
	 *
	 * @var array
	 */
	protected $options = array();


	/**
	 * Last response instance of the request after executing
	 *
	 * @var Response
	 */
	protected $lastResponse = null;


	/**
	 * Construct Request
	 *
	 * @param string $url
	 *
	 * @throws Exception
	 */
	public function __construct($url) {
		if ($url === null) {
			throw new Exception('Can not make HTTP request when URL is NULL');
		}
		
		if (!function_exists('curl_init')) {
			throw new Exception('CURL is not installed');
		}
		
		$this->url = $url;
		
		// default options
		$this->option(CURLOPT_RETURNTRANSFER, true);
	}


	/**
	 * Update the request's target URL
	 *
	 * @param string $url
	 * @return \Koldy\Http\Request
	 */
	public function url($url) {
		$this->url = $url;
		return $this;
	}


	/**
	 * Get the URL on which the request will be fired
	 *
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	
	/**
	 * Set the request type
	 * @param const $type
	 * @return \Koldy\Http\Request
	 * @example $request->type(\Koldy\Http\Request::POST);
	 */
	public function type($type) {
		$this->type = $type;
		return $this;
	}


	/**
	 * Get the request's type (method)
	 *
	 * @return string GET or POST (for now)
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * Set the request parameter
	 *
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return \Koldy\Http\Request
	 */
	public function param($key, $value) {
		$this->params[$key] = $value;
		return $this;
	}

	
	/**
	 * Set the parameters that will be sent. Any previously set parameters will be overriden.
	 * @param array $params
	 * @return \Koldy\Http\Request
	 */
	public function params(array $params) {
		$this->params = $params;
		return $this;
	}
	
	/**
	 * Get parameters that were set
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}
	
	/**
	 * Check if URL parameter is set
	 * @param string $key
	 * @return boolean
	 */
	public function hasParam($key) {
		return array_key_exists($key, $this->params);
	}
	
	/**
	 * Set the array of options. Array must be valid array with CURL constants as keys
	 * @param array $curlOptions
	 * @return \Koldy\Http\Request
	 * @link http://php.net/manual/en/function.curl-setopt.php
	 */
	public function options(array $curlOptions) {
		$this->options = $curlOptions;
		return $this;
	}
	
	/**
	 * Set the CURL option
	 * @param string $key
	 * @param mixed $value
	 * @return \Koldy\Http\Request
	 * @link http://php.net/manual/en/function.curl-setopt.php
	 */
	public function option($key, $value) {
		$this->options[$key] = $value;
		return $this;
	}
	
	/**
	 * Check if CURL option is set (exists in options array)
	 * @param string $key
	 * @return boolean
	 */
	public function hasOption($key) {
		return isset($this->options[$key]);
	}
	
	/**
	 * Execute request
	 * @throws Exception
	 * @return \Koldy\Http\Response
	 */
	public function exec() {
		$ch = curl_init();
		
		if ($this->type === static::POST) {
			curl_setopt($ch, CURLOPT_URL, $this->url);
			curl_setopt($ch, CURLOPT_POST, true); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->params);
		} else if ($this->type === static::GET && count($this->params) > 0) {
			$url = $this->url;
			if (strpos($url, '?') === false) {
				$url .= '?';
			} else {
				$url .= '&';
			}
			
			foreach ($this->params as $key => $value) {
				$url .= "{$key}=" . urlencode($value) . '&';
			}
			
			$url = substr($url, 0, -1);
			curl_setopt($ch, CURLOPT_URL, $url);
		} else {
			curl_setopt($ch, CURLOPT_URL, $this->url);
		}
		
		curl_setopt_array($ch, $this->options);
		$body = curl_exec($ch);
		
		if (curl_errno($ch)) {
			throw new Exception(curl_error($ch));
		} else {
			$info = curl_getinfo($ch);
		}
		
		curl_close($ch);
		$this->lastResponse = new Response($info, $body);
		return $this->lastResponse;
	}


	/**
	 * Get the last response object instance after executing the HTTP request
	 *
	 * @return Response
	 */
	public function getResponse() {
		return $this->lastResponse;
	}


	/**
	 * Make quick GET request
	 * @param string $url
	 * @param array $params [optional]
	 * @return \Koldy\Http\Response
	 * @example echo \Koldy\Http\Request::get('http://www.google.com') will output body HTML of google.com
	 */
	public static function get($url, array $params = array()) {
		$self = new static($url);
		$self->params($params);

		return $self->exec();
	}


	/**
	 * Make quick POST request
	 * 
	 * @param string $url
	 * @param array $params [optional]
	 * @return \Koldy\Http\Response
	 * @example echo \Koldy\Http\Request::post('http://www.google.com') will output body HTML of google.com
	 */
	public static function post($url, array $params = array()) {
		$self = new static($url);
		$self->type(static::POST);
		$self->params($params);

		return $self->exec();
	}


	/**
	 * Get (download) file from remote URL and save it on local path
	 * 
	 * @param string $remoteUrl
	 * @param string $localPath
	 * @return \Koldy\Http\Response
	 * @example Request::getFile('http://remote.com/path/to.gif', '/var/www/local/my.gif');
	 */
	public static function getFile($remoteUrl, $localPath) {
		Directory::mkdir(dirname($localPath), 0755);
		
		$fp = @fopen($localPath, 'wb');
		if (!$fp) {
			throw new Exception("Can not open file for writing: {$localPath}");
		}

		$ch = curl_init($remoteUrl);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 21600); // 6 hours should be enough, orrr??
		curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		fclose($fp);

		return new Response($info, null);
	}

}
