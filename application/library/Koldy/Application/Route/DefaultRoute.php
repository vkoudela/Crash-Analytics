<?php namespace Koldy\Application\Route;

use Koldy\Application;
use Koldy\Exception;
use Koldy\Log;

/**
 * I call this the default route because this will be just fine for the most
 * sites in the world. This class will parse and generate the URLs to the
 * following criteria:
 *
 * 	http://your.domain.com/[controller]/[action]/[param1]/[param2]/[paramN]
 *  or if module exists under that controller URL:
 *  http://your.domain.com/[module]/[controller]/[action]/[param1]/[param2]/[paramN]
 * 
 * Parameters can be caught with getVar() method by passing the position of it.
 * 
 * @link http://koldy.net/docs/routes/default-route
 */
class DefaultRoute extends AbstractRoute {


	/**
	 * The resolved module URL part
	 * 
	 * @var string
	 */
	protected $moduleUrl = null;


	/**
	 * The resolved controller URL part
	 * 
	 * @var string
	 * @example if URI is "/users/login", this will be "users"
	 */
	protected $controllerUrl = null;


	/**
	 * The resolved controller class name
	 * 
	 * @var string
	 */
	protected $controllerClass = null;


	/**
	 * The controller path
	 * 
	 * @var string
	 */
	private $controllerPath = null;


	/**
	 * The resolved action url
	 * 
	 * @var string
	 */
	protected $actionUrl = null;


	/**
	 * The resolved action method name
	 * 
	 * @var string
	 */
	protected $actionMethod = null;


	/**
	 * Flag if this request is an ajax request maybe?
	 * 
	 * @var boolean
	 */
	protected $isAjax = false;


	/**
	 * The controller's instance
	 * 
	 * @var object
	 */
	protected $controllerInstance = null;


	/**
	 * Construct the object
	 * 
	 * @param string $uri
	 */
	public function __construct($uri, array $config = null) {
		parent::__construct($uri, $config);

		// first, check the URI for duplicate slashes - they are not allowed
		// if you must pass duplicate slashes in URL, then urlencode them
		if (strpos($uri, '//') !== false) {
			header('Location: ' . str_replace('//', '/', $uri));
			exit(0);
		}

		$slash = DS;

		// There are two possible scenarios:
		// 1. The first part of URL leads to the module controller
		// 2. The first part of URL leads to the default controller
		
		if ($this->uri[1] == '') {
			$this->controllerUrl = 'index';
			$this->controllerClass = 'IndexController';
		} else {
			$this->controllerUrl = strtolower($this->uri[1]);
			$this->controllerClass = str_replace(' ', '', ucwords(str_replace(array('-', '.'), ' ', $this->controllerUrl))) . 'Controller';
		}

		// Now we have the controller class name detected, but, should it be
		// taken from module or from default controllers?
		
		$moduleDir = Application::getApplicationPath("modules{$slash}{$this->controllerUrl}");
		if (is_dir($moduleDir)) {

			// ok, it is a module with module/controller/action path
			$moduleUrl = $this->controllerUrl;
			$this->moduleUrl = $moduleUrl;

			if (isset($this->uri[2]) && $this->uri[2] != '') {
				$this->controllerUrl = strtolower($this->uri[2]);
				$this->controllerClass = str_replace(' ', '', ucwords(str_replace(array('-', '.'), ' ', $this->controllerUrl))) . 'Controller';
			} else {
				$this->controllerUrl = 'index';
				$this->controllerClass = 'IndexController';
			}

			$this->controllerPath = "{$moduleDir}{$slash}controllers{$slash}{$this->controllerClass}.php";
			$mainControllerExists = true;

			if (!is_file($this->controllerPath)) {
				// lets try with default controller when requested one is not here
				$this->controllerPath = Application::getApplicationPath("modules{$slash}{$moduleUrl}{$slash}controllers{$slash}IndexController.php");

				if (!is_file($this->controllerPath)) {
					// Even IndexController is missing. Can not resolve that.
					Log::notice("Can not find {$this->controllerClass} nor IndexController in {$moduleDir}{$slash}controllers");
					Application::error(404, 'Page not found');
				}

				$mainControllerExists = false;
				$this->controllerClass = 'IndexController';
			}

			if ($mainControllerExists) {
				if (!isset($this->uri[3]) || $this->uri[3] == '') {
					$this->actionUrl = 'index';
					$this->actionMethod = 'index';
				} else {
					$this->actionUrl = strtolower($this->uri[3]);
					$this->actionMethod = ucwords(str_replace(array('-', '.'), ' ', $this->actionUrl));
					$this->actionMethod = str_replace(' ', '', $this->actionMethod);
					$this->actionMethod = strtolower(substr($this->actionMethod, 0, 1)) . substr($this->actionMethod, 1);
				}
			} else if (isset($this->uri[2]) && $this->uri[2] != '') {
				$this->actionUrl = strtolower($this->uri[2]);
				$this->actionMethod = ucwords(str_replace(array('-', '.'), ' ', $this->actionUrl));
				$this->actionMethod = str_replace(' ', '', $this->actionMethod);
				$this->actionMethod = strtolower(substr($this->actionMethod, 0, 1)) . substr($this->actionMethod, 1);
			} else {
				$this->actionUrl = 'index';
				$this->actionMethod = 'index';
			}

			// and now, configure the include paths according to the case
			$basePath = Application::getApplicationPath();

			$includePath = array(
				// module stuff has greater priority then application folder itself

				"{$moduleDir}{$slash}controllers",
				"{$moduleDir}{$slash}modles",
				"{$moduleDir}{$slash}library",

				get_include_path()
			);

			set_include_path(implode(PATH_SEPARATOR, $includePath));

		} else {

			// ok, it is the default controller/action
			$this->controllerPath = Application::getApplicationPath("controllers{$slash}{$this->controllerClass}.php");

			$mainControllerExists = true;

			if (!is_file($this->controllerPath)) {
				$this->controllerPath = Application::getApplicationPath("controllers{$slash}IndexController.php");

				if (!is_file($this->controllerPath)) {
					// Even IndexController is missing. Can not resolve that.
					Log::notice("Can not find {$this->controllerClass} nor IndexController in " . Application::getApplicationPath('controllers'));
					Application::error(404, 'Page not found');
				}

				$mainControllerExists = false;
				$this->controllerClass = 'IndexController';
			}

			if ($mainControllerExists) {
				if (!isset($this->uri[2]) || $this->uri[2] == '') {
					$this->actionUrl = 'index';
					$this->actionMethod = 'index';
				} else {
					$this->actionUrl = strtolower($this->uri[2]);
					$this->actionMethod = ucwords(str_replace(array('-', '.'), ' ', $this->actionUrl));
					$this->actionMethod = str_replace(' ', '', $this->actionMethod);
					$this->actionMethod = strtolower(substr($this->actionMethod, 0, 1)) . substr($this->actionMethod, 1);
				}
			} else {
				$this->actionUrl = strtolower($this->uri[1]);
				$this->actionMethod = ucwords(str_replace(array('-', '.'), ' ', $this->actionUrl));
				$this->actionMethod = str_replace(' ', '', $this->actionMethod);
				$this->actionMethod = strtolower(substr($this->actionMethod, 0, 1)) . substr($this->actionMethod, 1);
			}

			// and now, configure the include paths according to the case
			$basePath = Application::getApplicationPath();
			Application::addIncludePath(array(
				$basePath . 'controllers',		// so you can extend abstract controllers in the same directory if needed,
				$basePath . 'models',			// all models should be in this directory
				$basePath . 'library'			// the place where you can define your own classes and methods
			));
		}

		$this->isAjax = (
			isset($_SERVER['REQUEST_METHOD'])
			&& $_SERVER['REQUEST_METHOD'] == 'POST'
			&& isset($_SERVER['HTTP_X_REQUESTED_WITH'])
			&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
		) || (
			isset($_SERVER['HTTP_ACCEPT'])
			&& strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
		);

		$controllerClassName = $this->getControllerClass();
		$controllerInstance = new $controllerClassName();

		$alwaysRestful = (isset($this->config['always_restful'])) && $this->config['always_restful'] === true;

		if ($alwaysRestful || (property_exists($controllerInstance, 'restful') && $controllerInstance::$restful === true)) {
			// it is restful
			$this->actionMethod = strtolower($_SERVER['REQUEST_METHOD']) . ucfirst($this->actionMethod);

		} else if ($this->isAjax) {
			$this->actionMethod .= 'Ajax';

		} else {
			$this->actionMethod .= 'Action';

		}

		$this->controllerInstance = $controllerInstance;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Application\Route\AbstractRoute::isAjax()
	 */
	public function isAjax() {
		return $this->isAjax;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Application\Route\AbstractRoute::getVar()
	 */
	public function getVar($whatVar, $default = null) {
		if (is_numeric($whatVar)) {
			$whatVar = (int) $whatVar +1;
			
			if (isset($this->uri[$whatVar])) {
				$value = trim($this->uri[$whatVar]);
				return ($value != '') ? $value : $default;
			} else {
				return $default;
			}
		} else {
			// if variable is string, then treat it like GET parameter
			if (isset($_GET[$whatVar])) {
				$value = trim($_GET[$whatVar]);
				return ($value != '') ? $value : $default;
			} else {
				return $default;
			}
		}
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Application\Route\AbstractRoute::getModuleUrl()
	 */
	public function getModuleUrl() {
		return $this->moduleUrl;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Application\Route\AbstractRoute::getControllerUrl()
	 */
	public function getControllerUrl() {
		return $this->controllerUrl;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Application\Route\AbstractRoute::getControllerClass()
	 */
	public function getControllerClass() {
		return $this->controllerClass;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Application\Route\AbstractRoute::getActionUrl()
	 */
	public function getActionUrl() {
		return $this->actionUrl;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Application\Route\AbstractRoute::getActionMethod()
	 */
	public function getActionMethod() {
		return $this->actionMethod;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Application\Route\AbstractRoute::href()
	 */
	public function href($controller = null, $action = null, array $params = null) {
		if ($controller !== null && strpos($controller, '/') !== false) {
			throw new \InvalidArgumentException('Slash is not allowed in controller name');
		}

		if ($action !== null && strpos($action, '/') !== false) {
			throw new \InvalidArgumentException('Slash is not allowed in action name');
		}

		$config = Application::getConfig();
		if ($controller === null) {
			$controller = '';
		}

		$url = $config['site_url'];
		$url .= '/' . $controller;

		if ($action !== null) {
			$url .= '/' . $action;
		}

		if ($params !== null && count($params) > 0) {
			$q = array();
			foreach ($params as $key => $value) {
				if (is_numeric($key)) {
					$url .= '/' . $value;
				} else {
					$q[$key] = $value;
				}
			}
			
			if (sizeof($q) > 0) {
				$url .= '?';
				foreach ($q as $key => $value) {
					$url .= "{$key}={$value}&";
				}
				$url = substr($url, 0, -1);
			}
		}

		return $url;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Application\Route\AbstractRoute::exec()
	 */
	public function exec() {
		if (method_exists($this->controllerInstance, 'before')) {
			$response = $this->controllerInstance->before();
			// if "before" method returns anything, then we should not continue
			if ($response !== null) {
				return $response;
			}
		}

		$method = $this->getActionMethod();
		if (method_exists($this->controllerInstance, $method) || method_exists($this->controllerInstance, '__call')) {
			// get the return value of your method (json, xml, view object, download, string or nothing)
			return $this->controllerInstance->$method();

		} else {
			// the method we need doesn't exists, so, there is nothing we can do about it any more
			Log::notice("Can not find method={$method} in class={$this->getControllerClass()} on path={$this->controllerPath} for URI=" . Application::getUri());
			static::error(404);
		}
	}

}
