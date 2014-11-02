<?php namespace Koldy;

/**
 * This is another utility class that know how to handle URL. While developing
 * your site, you'll probably need to generate URL and detect if you're
 * currently on some given URL. This class provides all of it.
 * 
 * This class relies on your route instance so you'll probably need to check
 * the docs of your routes to understand the methods below.
 */
class Url {


	/**
	 * Get the variable from request. This depends about the route you're using.
	 *
	 * @param string|int $whatVar
	 * @param string|int $default
	 *
	 * @return string|int
	 */
	public static function getVar($whatVar, $default = null) {
		return Application::route()->getVar($whatVar, $default);
	}


	/**
	 * Get the controller name in the exact format as its being used in URL
	 *
	 * @return string
	 */
	public static function controller() {
		return Application::route()->getControllerUrl();
	}


	/**
	 * Is given controller the current working controller?
	 * 
	 * @param string $controller the url format (e.g. "index"), not the class name such as "IndexController"
	 * @return boolean
	 */
	public static function isController($controller) {
		return ($controller == Application::route()->getControllerUrl());
	}


	/**
	 * Get the current action in the exact format as it is being used in URL
	 *
	 * @return string
	 */
	public static function action() {
		return Application::route()->getActionUrl();
	}


	/**
	 * Is given action the current working action?
	 * 
	 * @param string $action the url format (e.g. "index"), not the method name such as "indexAction"
	 * @return boolean
	 */
	public static function isAction($action) {
		return ($action == Application::route()->getActionUrl());
	}


	/**
	 * Are given controller and action current working controller and action?
	 * 
	 * @param string $controller in the url format
	 * @param string $action in the url format
	 * @return boolean
	 */
	public static function is($controller, $action) {
		return ($controller == Application::route()->getControllerUrl() && $action == Application::route()->getActionUrl());
	}


	/**
	 * Is this the matching module, controller and action?
	 * 
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @return boolean
	 */
	public static function isModule($module, $controller = null, $action = null) {
		$route = Application::route();
		if ($module === $route->getModuleUrl()) {
			if ($controller === null) {
				return true;
			} else {
				if ($controller === $route->getControllerUrl()) {
					// now we have matched module and controller
					if ($action === null) {
						return true;
					} else {
						return ($action === $route->getActionUrl());
					}
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}


	/**
	 * Get the complete current URL with domain and protocol and request URI
	 * 
	 * @return null|string will return NULL in CLI environment
	 */
	public static function current() {
		if (!isset($_SERVER['REQUEST_URI'])) {
			return null;
		}

		return Application::getConfig('application', 'site_url') . $_SERVER['REQUEST_URI'];
	}


	/**
	 * Generate the link suitable for <a> tags. Generating links depends about the routing class you're using.
	 *
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 *
	 * @return string
	 */
	public static function href($controller = null, $action = null, array $params = null) {
		return Application::route()->href($controller, $action, $params);
	}


	/**
	 * Generate the link to home page
	 * 
	 * @return string
	 */
	public static function home() {
		return static::href();
	}


	/**
	 * Generate the link to static asset on the same host where application is. This method is using link() method in
	 * routing class, so be careful because it might be overriden in your case.
	 *
	 * @param string $path
	 * @return string
	 */
	public static function link($path) {
		return Application::route()->link($path);
	}


	/**
	 * Generate URL for asset file depending on configuration for assets located in configs/application.php 'cdn_url'.
	 * This method is using cdn() method in routing class, so be careful because it might be overriden in your case.
	 *
	 * @param string $path
	 * @return string
	 */
	public static function cdn($path) {
		return Application::route()->cdn($path);
	}

}
