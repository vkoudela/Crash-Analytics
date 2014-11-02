<?php namespace Koldy\Application\Route;

use Koldy\Application;
use Koldy\Exception;

/**
 * I call this the default route because this will be just fine for the most
 * sites in the world. This class will parse and generate the URLs to the
 * following criteria:
 *
 * 	http://your.domain.com/[controller]/[action]/[param1]/[param2]/[paramN]
 *  or if module exists under that controller URL:
 *  http://your.domain.com/[module]/[controller]/[action]/[param1]/[param2]/[paramN]
 * 
 * 
 * TODO: Finish this! Not tested! damn
 */
class OldStyle extends AbstractRoute {


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
	 * Construct the object
	 * @param string $uri
	 */
	public function __construct($uri, array $config = null) {
		if ($config === null || count($config) == 0 || !isset($config['module_param']) || !isset($config['controller_param']) || !isset($config['action_param'])) {
			throw new Exception('Route config options are missing');
		}

		parent::__construct($uri, $config);

		if (isset($_GET[$config['controller_param']])) {
			$c = trim($_GET[$config['controller_param']]);
			if ($c == '') {
				$this->controllerUrl = 'index';
				$this->controllerClass = 'IndexController';
			} else {
				$this->controllerUrl = strtolower($c);
				$this->controllerClass = str_replace(' ', '', ucwords(str_replace(array('-', '.'), ' ', $this->controllerUrl))) . 'Controller';
			}
		} else {
			$this->controllerUrl = 'index';
			$this->controllerClass = 'IndexController';
		}
		
		// Now we have the controller class name detected, but, should it be
		// taken from module or from default controllers?

		// TODO: Zavrsiti ovo!
		$moduleDir = Application::getApplicationPath() . 'modules' . DS . $this->controllerUrl;
		if (is_dir($moduleDir)) {
			// ok, it is a module with module/controller/action path
			$moduleUrl = $this->controllerUrl;
			$this->moduleUrl = $moduleUrl;
			
			$a = isset($_GET[$config['action_param']]) ? trim($_GET[$config['action_param']]) : '';
			if ($a != '') {
				$this->controllerUrl = strtolower($a);
				$this->controllerClass = str_replace(' ', '', ucwords(str_replace(array('-', '.'), ' ', $this->controllerUrl))) . 'Controller';
			} else {
				$this->controllerUrl = 'index';
				$this->controllerClass = 'IndexController';
			}
			$this->controllerPath = $moduleDir . DS . 'controllers' . DS . $this->controllerClass . '.php';
			
			$mainControllerExists = true;

			if (!is_file($this->controllerPath)) {
				$this->controllerPath = Application::getApplicationPath() . 'modules' . DS . $moduleUrl . DS . 'controllers' . DS
					. 'IndexController.php';

				if (!is_file($this->controllerPath)) {
					// Even IndexController is missing. Can not resolve that.
					if (Application::inDevelopment()) {
						$controllersPath = $moduleDir . DS . 'controllers';
						\Koldy\Log::debug("Can not find {$this->controllerClass} nor IndexController in {$controllersPath}");
					}
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
			Application::addIncludePath(array(
				// module paths has higher priority then default stuff
				$moduleDir . DS . 'controllers' . DS,
				$moduleDir . DS . 'models' . DS,
				$moduleDir . DS . 'library' . DS,
				Application::getApplicationPath() . 'controllers' . DS, // so you can extend abstract controllers in the same directory if needed,
				Application::getApplicationPath() . 'models' . DS, // all models should be in this directory
				Application::getApplicationPath() . 'library' . DS, // the place where you can define your own classes and methods
			));
		} else {
			// ok, it is the default controller/action
			$this->controllerPath = Application::getApplicationPath() . 'controllers' . DS
				. $this->controllerClass . '.php';

			$mainControllerExists = true;

			if (!is_file($this->controllerPath)) {
				$this->controllerPath = Application::getApplicationPath() . 'controllers' . DS
					. 'IndexController.php';

				if (!is_file($this->controllerPath)) {
					// Even IndexController is missing. Can not resolve that.
					if (Application::inDevelopment()) {
						$controllersPath = Application::getApplicationPath() . 'controllers';
						\Koldy\Log::debug("Can not find {$this->controllerClass} nor IndexController in {$controllersPath}");
					}
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
			Application::addIncludePath(array(
				Application::getApplicationPath() . 'controllers' . DS, // so you can extend abstract controllers in the same directory if needed,
				Application::getApplicationPath() . 'models' . DS, // all models should be in this directory
				Application::getApplicationPath() . 'library' . DS // the place where you can define your own classes and methods
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

		if ($this->isAjax) {
			$this->actionMethod .= 'Ajax';
		} else {
			$this->actionMethod .= 'Action';
		}
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
			throw new Exception('Slash is not allowed in controller name');
		}

		if ($action !== null && strpos($action, '/') !== false) {
			throw new Exception('Slash is not allowed in action name');
		}

		$config = Application::getConfig();
		if ($controller === null) {
			$controller = '';
		}

		$url = $config['site_url'] . '/?';

		$url .= "{$this->config['controller_param']}={$controller}";

		if ($action !== null) {
			$url .= "&{$this->config['action_param']}={$action}";
		}

		if ($params !== null) {
			foreach ($params as $key => $value) {
				$url .= "&{$key}={$value}";
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
			Log::notice("Can not find method={$method} in class={$this->getControllerClass()} on path={$this->controllerPath}");
			static::error(404);
		}
	}

}