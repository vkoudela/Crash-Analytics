<?php namespace Koldy\Http;
/**
 * This will be the instance of the response created by \Koldy\Http\Request class
 *
 */
class Response {


	/**
	 * The info from CURL
	 * 
	 * @var array
	 * @link http://www.php.net/manual/en/function.curl-getinfo.php#refsect1-function.curl-getinfo-returnvalues
	 */
	protected $info = null;


	/**
	 * The response body from request
	 * 
	 * @var string
	 */
	protected $body = null;


	public function __construct(array $curlInfo, $responseBody) {
		$this->info = $curlInfo;
		$this->body = $responseBody;
	}


	/**
	 * What was the request URL?
	 * 
	 * @return string
	 */
	public function url() {
		return $this->info['url'];
	}


	/**
	 * Get the response body
	 * 
	 * @return string
	 */
	public function body() {
		return $this->body;
	}


	/**
	 * What is the response HTTP code?
	 * 
	 * @return int
	 */
	public function httpCode() {
		return (int) $this->info['http_code'];
	}


	/**
	 * Is response OK? (is HTTP response code 200)
	 * 
	 * @return boolean
	 */
	public function isOk() {
		return $this->httpCode() == 200;
	}


	/**
	 * Get the content type of response
	 * 
	 * @return string
	 */
	public function contentType() {
		return $this->info['content_type'];
	}


	/**
	 * Get the request's connect time in seconds
	 * 
	 * @return float
	 */
	public function connectTime() {
		return $this->info['connect_time'];
	}


	/**
	 * Get the request's connect time in miliseconds
	 * 
	 * @return int
	 */
	public function connectTimeMs() {
		return round($this->info['connect_time'] * 1000);
	}


	/**
	 * Get the request total time in seconds
	 * 
	 * @return float
	 */
	public function totalTime() {
		return $this->info['total_time'];
	}


	/**
	 * Get the request total time in miliseconds
	 * 
	 * @return int
	 */
	public function totalTimeMs() {
		return round($this->info['total_time'] * 1000);
	}


	/**
	 * If you try to print the response object, you'll get response body
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->body;
	}

}
