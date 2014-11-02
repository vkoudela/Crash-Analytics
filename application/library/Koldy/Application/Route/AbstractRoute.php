<?php namespace Koldy\Application\Route;

use Koldy\Application;
use Koldy\Exception;
use Koldy\Log;
use Koldy\Json;

/**
 * To create your own routing system, you must extend this class and then,
 * in config/application.php under 'routing_class' set the name of your own class.
 * 
 * Routing class must do the following:
 * - parse request URI and determine whats controller, action and parameters
 * - generate proper URLs with controller, action and parameters
 * - handle the error pages
 * - handle the exceptions
 * 
 */
abstract class AbstractRoute {


	/**
	 * The URI that is initialized. Do not rely on $_SERVER['REQUEST_URI'].
	 * 
	 * @var string
	 */
	protected $uri = null;


	/**
	 * The route config defined in config/application.php. This property will be
	 * always an array and you should use it as array
	 * 
	 * @var array
	 */
	protected $config = null;


	/**
	 * Construct the object
	 * 
	 * @param string $uri by default is $_SERVER['REQUEST_URI'] but it can be set manually in Application's run() method
	 * @param array $config [optional]
	 * @example parameter might be "/user/login"
	 */
	public function __construct($uri, array $config = null) {
		$this->uri = explode('/', $uri);
		$this->config = ($config === null) ? array() : $config;
	}


	/**
	 * Get the module URL part
	 * 
	 * @return string
	 */
	abstract public function getModuleUrl();


	/**
	 * Get the controller as it is in URI
	 * 
	 * @return string
	 */
	abstract public function getControllerUrl();


	/**
	 * What is the controller class name got from URI. When routing class resolves
	 * the URI, then you'll must have this info, so, return that name.
	 * 
	 * @return string
	 */
	abstract public function getControllerClass();


	/**
	 * Get the "action" part as it is URI
	 * 
	 * @return string
	 * @example if URI is "/user/login", then this might return "login" only
	 */
	abstract public function getActionUrl();


	/**
	 * What is the action method name resolved from from URI and request type
	 * 
	 * @return string
	 * @example if URI is "/user/show-details/5", then this might return "showDetailsAction"
	 */
	abstract public function getActionMethod();


	/**
	 * Get the variable from the URL
	 * 
	 * @param mixed $whatVar
	 * @param string $default [optional] if variable doesn't exists in request
	 * @return mixed
	 */
	abstract public function getVar($whatVar, $default = null);


	/**
	 * If route knows how to detect language, then override this method.
	 * 
	 * @return mixed
	 */
	public function getLanguage() {
		return null;
	}


	/**
	 * Is this request Ajax request or not? This is used in \Koldy\Application when printing
	 * error or exception
	 * 
	 * @return boolean or null if feature is not implemented
	 */
	public function isAjax() {
		return null;
	}


	/**
	 * Generate link to another page
	 * 
	 * @param string $controller
	 * @param string $action [optional]
	 * @param array $params [optional]
	 * @return string
	 */
	abstract public function href($controller, $action = null, array $params = null);


	/**
	 * Generate link to the resource file on the same domain
	 * 
	 * @param string $path
	 * @return string
	 */
	public function link($path) {

		// if you pass the full URL that contains "://" part, then it will be immediately
		// returned without any kind of building or parsing it

		$pos = strpos($path, '://');
		if ($pos !== false && $pos < 10) {
			return $path;
		}

		$config = Application::getConfig();
		
		if ($path[0] != '/') {
			$path = '/' . $path;
		}
		
		return $config['site_url'] . $path;
	}


	/**
	 * Generate link to the resource file on CDN domain if CDN is set, otherwise
	 * this acts the same as link() method
	 * 
	 * @param string $path
	 * @return string
	 */
	public function cdn($path) {

		// if you pass the full URL that contains "://" part, then it will be immediately
		// returned without any kind of building or parsing it

		$pos = strpos($path, '://');
		if ($pos !== false && $pos < 10) {
			return $path;
		}
		
		$config = Application::getConfig();
		$cdnUrl = ($config['cdn_url'] === null) ? $config['site_url'] : $config['cdn_url'];
		
		if ($path[0] != '/') {
			$path = '/' . $path;
		}

		if (substr($path, 0, 2) == '//') {
			return $path;
		}
		
		return $cdnUrl . $path;
	}


	/**
	 * And now, execute the Controller->methodAction() detected in routing class
	 * and return stuff, or throw exception, or show error.
	 * 
	 * @return mixed
	 */
	abstract public function exec();


	/**
	 * If your app throws any kind of exception, it will end up here, so, handle it!
	 * 
	 * @param \Exception $e
	 */
	public function handleException(\Exception $e) {
		if (!headers_sent()) {
			header('HTTP/1.1 503 Service Temporarily Unavailable', true, 503);
			header('Status: 503 Service Temporarily Unavailable');
			header('Retry-After: 300'); // 300 seconds / 5 minutes
		}
		
		if ($this->isAjax()) {

			Json::create(array(
				'success' => false,
				'type' => 'exception',
				'exception' => Application::inDevelopment() ? $e->getMessage() : null,
				'trace' => Application::inDevelopment() ? $e->getTraceAsString() : null
			))
			->flush();

		} else {

			$file503 = Application::getPublicPath('503.php');
				
			if (is_file($file503)) {
				$code = 503;
				$message = $e->getMessage();
				$exception = $e;
				include $file503;

			} else {
				if (Application::inDevelopment()) {
					echo "<strong>{$e->getMessage()}</strong><pre>{$e->getTraceAsString()}</pre>";
				} else {
					echo "<h1>Error</h1><p>Something went wrong. Please try again later!</p>";
				}
			}
		}
		
		Log::exception($e);
	}


	/**
	 * I'm sure you sometimes want to show nice error to user! Well, if you call
	 * Application::error(404), then it will end up here. This kind of errors are meant
	 * only to show nice error to user. If you also want to log this or alert anyone that
	 * this happened, then do that before calling Application::error() method.
	 * 
	 * @param int $code The HTTP error code
	 * @param string $message Optional message that will be visible to user
	 * @param \Exception $e Optional exception, if any. Be careful, you might not
	 * want to show the exceptions to users, but you would like to show it to developers? Then
	 * use Application::inDevelopment() and inProduction() methods.
	 */
	public function error($code, $message = null, \Exception $e = null) {
		if (!headers_sent()) {
			switch($code) {
				case 400:
					header('HTTP/1.0 400 Bad Request', true, 400);

					if ($message === null) {
						$message = 'Bad request';
					}
					break;

				case 403:
					header('HTTP/1.0 403 Forbidden', true, 403);

					if ($message === null) {
						$message = 'Forbidden';
					}
					break;

				case 404:
					header('HTTP/1.0 404 Not Found', true, 404);

					if ($message === null) {
						$message = 'Page Not Found';
					}
					break;

				case 500:
					header('HTTP/1.0 500 Internal Server Error', true, 500);

					if ($message === null) {
						$message = 'Internal Server Error';
					}
					break;

				case 503:
					header('HTTP/1.1 503 Service Temporarily Unavailable', true, 503);
					header('Status: 503 Service Temporarily Unavailable');
					header('Retry-After: 300'); // 300 seconds / 5 minutes

					if ($message === null) {
						$message = 'Service Temporarily Unavailable';
					}
					break;
			}
		}

		if ($message === null) {
			$message = 'Page Not Found';
		}

		if ($this->isAjax()) {
			$data = array(
				'success' => false,
				'type' => 'http',
				'code' => $code,
				'message' => $message,
				'exception' => (($e !== null && Application::inDevelopment()) ? $e->getMessage() : null)
			);

			Json::create($data)->flush();

		} else {
			$path = Application::getPublicPath("{$code}.php");
			if (file_exists($path)) {
				$exception = $e;
				include $path;
			} else {
				// i don't know how to handle this message now!?
				throw new Exception($message === null ? ('Error ' . $code) : $message);				
			}

		}

		exit(0);
	}

}
