<?php

class BootstrapUI {
	
	/**
	 * CSS dependencies
	 * @var array
	 */
	private static $dependencyCss = array();
	
	/**
	 * Javascript dependencies
	 * @var array
	 */
	private static $dependencyJavascript = array(
		'js/php/base64_encode.js',
		'js/php/base64_decode.js',
		'js/php/in_array.js',
		'js/php/json_encode.js',
		'js/php/json_decode.js',
		'js/BootstrapUI.js',
		'js/BootstrapUI/form.js',
		'js/BootstrapUI/table/remote.js',
		'js/BootstrapUI/panel.js',
		'js/BootstrapUI/button/remote.js'
	);
	
	/**
	 * Add additional resource
	 * @param string $url
	 */
	public static function addDependency($url) {
		if (is_array($url)) {
			foreach ($url as $e) {
				static::addDependency($e);
			}
		} else {
			$extension = strtolower(substr($url, strrpos($url, '.') +1));
			if ($extension == 'js') {
				if (!in_array($url, static::$dependencyJavascript)) {
					static::$dependencyJavascript[] = $url;
				}
			} else if ($extension == 'css') {
				if (!in_array($url, static::$dependencyCss)) {
					static::$dependencyCss[] = $url;
				}
			}
		}
	}
	
	/**
	 * @return array
	 */
	public static function getDependencyCss() {
		return static::$dependencyCss;
	}
	
	/**
	 * @return array
	 */
	public static function getDependencyJavascript() {
		return static::$dependencyJavascript;
	}
	
	/**
	 * Print dependencies into HTML
	 */
	public static function printDependencies() {
		echo "\n";
		foreach (static::$dependencyCss as $link) {
			echo "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"{$link}\"/>\n";
		}
		foreach (static::$dependencyJavascript as $link) {
			echo "\t<script type=\"text/javascript\" src=\"{$link}\"></script>\n";
		}
	}
	
	/**
	 * New form object
	 * @param string $action
	 * @return \BootstrapUI\Form
	 */
	public static function form($action = null) {
		return new BootstrapUI\Form($action);
	}
	
	/**
	 * New form response object
	 * @return \BootstrapUI\Response\Form
	 */
	public static function formResponse() {
		return new BootstrapUI\Response\Form();
	}
	
	/**
	 * New remote table object
	 * @param string $url
	 * @return \BootstrapUI\Table\Remote
	 */
	public static function tableRemote($url = null) {
		return new BootstrapUI\Table\Remote($url);
	}
	
	/**
	 * New table remote response object
	 * @return \BootstrapUI\Response\TableRemote
	 */
	public static function tableRemoteResponse() {
		return new BootstrapUI\Response\TableRemote();
	}
	
	/**
	 * New remote button
	 * @param string $text
	 * @return \BootstrapUI\Button\Remote
	 */
	public static function buttonRemote($text) {
		return new BootstrapUI\Button\Remote($text);
	}
	
	/**
	 * Remote button response
	 * @return \BootstrapUI\Response\ButtonRemote
	 */
	public static function buttonRemoteResponse() {
		return new BootstrapUI\Response\ButtonRemote();
	}
	
	/**
	 * The Select2 component
	 * @param string $name
	 * @param string $label
	 * @param array $values
	 * @param string $value
	 * @return \BootstrapUI\Input\Select2
	 */
	public static function select2($name, $label = null, array $values = array(), $value = null) {
		return new BootstrapUI\Input\Select2($name, $label, $values, $value);
	}
}