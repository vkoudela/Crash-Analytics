<?php namespace Koldy;

/**
 * This is some kind of "wrapper" for $_SERVER. You can fetch some useful
 * informations with this class. And it is more robust.
 * 
 * We really recommend that you use this class instead of $_SERVER variables directly.
 *
 */
class Request {


	/**
	 * Cache the detected real IP so we don't iterate everything on each call
	 * 
	 * @var string
	 */
	private static $realIp = null;


	/**
	 * Get the real ip address of remote user
	 * 
	 * @return string
	 */
	public static function ip() {
		if (static::$realIp !== null) {
			return static::$realIp;
		}

		$possibilities = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR'
		);

		foreach ($possibilities as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					$ip = trim($ip);

					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
						static::$realIp = $ip;
						return $ip;
					}
				}
			}
		}

		if (defined('KOLDY_CLI') && KOLDY_CLI === true) {
			static::$realIp = '127.0.0.1';
		} else if (isset($_SERVER['REMOTE_ADDR'])) {
			static::$realIp = $_SERVER['REMOTE_ADDR'];
		} else {
			static::$realIp = false;
		}

		return static::$realIp;
	}


	/**
	 * Is current IP address of incoming request IPv6?
	 * 
	 * @return boolean
	 */
	public static function isIPv6() {
		$ip = static::ip();
		return strpos($ip, ':') !== false;
	}


	/**
	 * Get the ip address of server
	 * 
	 * @return string
	 */
	public static function serverIp() {
		return isset($_SERVER['SERVER_ADDR'])
			? $_SERVER['SERVER_ADDR']
			: '127.0.0.1';
	}


	/**
	 * Get the host name of request or null
	 * 
	 * @return string
	 */
	public static function hostName() {
		if (isset($_SERVER['HTTP_HOST'])) {
			return $_SERVER['HTTP_HOST'];
		} else {
			$siteUrl = Application::getConfig('application', 'site_url');
			return substr($siteUrl, strpos($siteUrl, '//') +2);
		}
	}


	/**
	 * If your running your site on some.something.domain.com, then this will return only "domain.com".
	 * But if you're running your site on some.something.domain.com.hr, then this will fail :/
	 * 
	 * @return string
	 * @deprecated because its not reliable
	 */
	public static function hostNameDomain() {
		if (!isset($_SERVER['HTTP_HOST'])) {
			return null;
		}

		if ($_SERVER['HTTP_HOST'] == $_SERVER['REMOTE_ADDR']) {
			return $_SERVER['REMOTE_ADDR'];
		}

		$domain = explode('.', $_SERVER['HTTP_HOST']);
		$size = count($domain);

		if ($size <= 2) {
			return $_SERVER['HTTP_HOST'];
		} else {
			return "{$domain[$size -2]}.{$domain[$size -1]}";
		}
	}


	/**
	 * Get the host name of remote user. This will use gethostbyaddr function
	 * 
	 * @return string|null
	 * @link http://php.net/manual/en/function.gethostbyaddr.php
	 */
	public static function host() {
		$host = gethostbyaddr(self::ip());
		return ($host == '') ? null : $host;
	}


	/**
	 * Are there proxy headers detected?
	 * 
	 * @return bool
	 */
	public static function hasProxy() {
		return (isset($_SERVER['HTTP_VIA']) || isset($_SERVER['HTTP_X_FORWARDED_FOR']));
	}


	/**
	 * Get proxy signature
	 * 
	 * @return string|null
	 * @example 1.1 example.com (squid/3.0.STABLE1)
	 */
	public static function proxySignature() {
		if (isset($_SERVER) && isset($_SERVER['HTTP_VIA'])) {
			return $_SERVER['HTTP_VIA'];
		}

		return null;
	}


	/**
	 * Get the IP address of proxy server if exists
	 * 
	 * @return string|null
	 */
	public static function proxyForwardedFor() {
		if (isset($_SERVER) && isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		return null;
	}


	/**
	 * Get remote IP address with additional IP sent over proxy if exists
	 * 
	 * @param string $delimiter
	 * @return string
	 * @example 89.205.104.23;10.100.10.190
	 */
	public static function ipWithProxy($delimiter = ',') {
		$ip = self::ip();
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $ip != $_SERVER['HTTP_X_FORWARDED_FOR']) {
			$ip .= "{$delimiter}{$_SERVER['HTTP_X_FORWARDED_FOR']}";
		}

		return $ip;
	}


	/**
	 * Get HTTP VIA header
	 * 
	 * @return string|null
	 * @example 1.0 200.63.17.162 (Mikrotik HttpProxy)
	 */
	public static function httpVia() {
		return (isset($_SERVER['HTTP_VIA']))
			? $_SERVER['HTTP_VIA']
			: null;
	}


	/**
	 * Get HTTP X_FORWARDED_FOR header
	 * 
	 * @return string|null
	 * @example 58.22.246.105
	 */
	public static function httpXForwardedFor() {
		return (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			? $_SERVER['HTTP_X_FORWARDED_FOR']
			: null;
	}


	/**
	 * Get the user agent
	 * 
	 * @return string or null if not set
	 */
	public static function userAgent() {
		return isset($_SERVER['HTTP_USER_AGENT'])
			? $_SERVER['HTTP_USER_AGENT']
			: null;
	}


	/**
	 * Get request URI string
	 * 
	 * @return string or null if doesn't exists
	 */
	public static function uri() {
		return isset($_SERVER['REQUEST_URI'])
			? $_SERVER['REQUEST_URI']
			: null;
	}


	/**
	 * Get HTTP referer if set
	 * 
	 * @return string or null if not set
	 */
	public static function httpReferer() {
		return isset($_SERVER['HTTP_REFERER'])
			? $_SERVER['HTTP_REFERER']
			: null;
	}


	/**
	 * Is POST request?
	 * 
	 * @return boolean
	 */
	public static function isPost() {
		return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST';
	}


	/**
	 * Is GET request?
	 * 
	 * @return boolean
	 */
	public static function isGet() {
		return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET';
	}


	/**
	 * Is PUT request?
	 * 
	 * @return boolean
	 */
	public static function isPut() {
		return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'PUT';
	}


	/**
	 * Is DELETE request?
	 * 
	 * @return boolean
	 */
	public static function isDelete() {
		return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'DELETE';
	}


	/**
	 * Is this "request" being executed as script in CLI environment?
	 * 
	 * @return boolean
	 */
	public static function isCli() {
		return (defined('KOLDY_CLI') && KOLDY_CLI === true);
	}

}
