<?php namespace Koldy;

/**
 * If you want to return your own view object from controllers, then
 * extend this class.
 */
abstract class Response {


	/**
	 * The function that should be called when script finishes output
	 * 
	 * @var function
	 */
	protected $workAfterResponse = null;


	/**
	 * The array of headers that will be printed before outputing anyting
	 * 
	 * @var array
	 */
	private $headers = array();


	/**
	 * The HTTP response headers - it can be only one code response
	 * @var array
	 */
	private $httpHeader = array(
		'code' => null,
		'status' => null,
		'override' => null
	);


	/**
	 * Flush the content to output buffer
	 */
	abstract public function flush();


	/**
	 * Set response header
	 * 
	 * @param string $name
	 * @param string $value [optional]
	 * @return \Koldy\Response
	 */
	public function header($name, $value = null) {
		if (!is_string($name)) {
			throw new \InvalidArgumentException('Invalid header name: ' . $name);
		}

		if ($value !== null && is_array($value)) {
			throw new \InvalidArgumentException("Invalid header value for name={$name}; expected string, got array");
		}

		$this->headers[] = array(
			'one-line' => ($value === null),
			'name' => $name,
			'value' => $value
		);

		return $this;
	}


	/**
	 * Set the HTTP response header with status code
	 * 
	 * @param int $httpCode
	 * @param string $httpStatus
	 * @param boolean $override override the already set HTTP code
	 * @return \Koldy\Response
	 */
	public function httpHeader($httpCode, $httpStatus, $override = true) {
		if ($httpCode < 100 || $httpCode > 999) {
			throw new \InvalidArgumentException('Invalid HTTP code while setting HTTP header');
		}

		if (!is_string($httpStatus)) {
			throw new \InvalidArgumentException('Invalid HTTP status while setting HTTP header');
		}

		$this->httpHeader = array(
			'code' => $httpCode,
			'status' => $httpStatus,
			'override' => $override
		);

		return $this;
	}


	/**
	 * Get the HTTP response code that will be used when object is flushed.
	 * 
	 * @return int or null if code is not set (if null, then web server will throw the http code (usually 200))
	 */
	public function getHttpCode() {
		return $this->httpHeader['code'];
	}


	/**
	 * Is header already set
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function hasHeader($name) {
		foreach ($this->headers as $header) {
			if (!$header['one-line'] && $header['name'] == $name) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Remove the header by name
	 * 
	 * @param string $name
	 * @return \Koldy\Response
	 */
	public function removeHeader($name) {
		foreach ($this->headers as $index => $header) {
			if ($header['name'] == $name) {
				unset($this->headers[$index]);
				return $this;
			}
		}

		return $this;
	}


	/**
	 * Remove all headers
	 * 
	 * @return \Koldy\Response
	 */
	public function removeHeaders() {
		$this->headers = array();
		return $this;
	}


	/**
	 * Flush the headers
	 */
	public function flushHeaders() {
		if (!headers_sent()) {
			// first flush the HTTP header first, if any

			if ($this->httpHeader['code'] !== null) {
				header($this->httpHeader['status'], $this->httpHeader['override'], $this->httpHeader['code']);
			}

			foreach ($this->headers as $header) {
				header("{$header['name']}: {$header['value']}");
			}
		} else {
			Log::error('Can\'t flushHeaders because headers are already sent');
		}
	}


	/**
	 * Get the array of all headers (one item is one header)
	 * 
	 * DO NOT USE THIS data for flushing the headers later! If you want to
	 * flush the headers, use flushHeaders() method!
	 * 
	 * @return array
	 */
	public function getHeaders() {
		$headers = array();

		if ($this->httpHeader['code'] !== null) {
			$headers[] = $this->httpHeader['status'];
		}

		foreach ($this->headers as $header) {
			$headers[] = $header['one-line'] ? $header['value'] : "{$header['name']}: {$header['value']}";
		}

		return $headers;
	}


	/**
	 * Set the function for after work
	 * 
	 * @param function $function
	 * @return \Koldy\Response
	 */
	public function after($function) {
		if (!is_object($function) || !($function instanceof \Closure)) {
			throw new Exception('You must pass the function to after method in ' . get_class($this) . ' class');
		}

		$this->workAfterResponse = $function;
		return $this;
	}

}
