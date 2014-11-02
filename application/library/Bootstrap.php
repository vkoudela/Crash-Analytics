<?php

class Bootstrap {
	
	/**
	 * The Bootstrap CSS dependencies
	 * @var array
	 */
	private static $dependencyCss = array(
		'css/bootstrap.min.css'
	);
	
	/**
	 * The Bootstrap Javascript dependencies
	 * @var array
	 */
	private static $dependencyJavascript = array(
		'js/bootstrap.min.js'
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
	 * New container
	 * @return \Bootstrap\Container
	 * @link http://getbootstrap.com/css/#overview
	 */
	public static function container() {
		return new Bootstrap\Container();
	}
	
	/**
	 * New row element
	 * @return \Bootstrap\Row
	 * @link http://getbootstrap.com/css/#grid
	 */
	public static function row() {
		return new Bootstrap\Row();
	}
	
	/**
	 * New heading element (h1, h2, ...)
	 * @param int $size 1-6
	 * @param string $text
	 * @return \Bootstrap\Heading
	 * @link http://getbootstrap.com/css/#type-headings
	 */
	public static function h($size, $text = null) {
		return new Bootstrap\Heading($size, $text);
	}
	
	/**
	 * New paragraph <p>
	 * @param string $text
	 * @return \Bootstrap\Paragraph
	 * @link http://getbootstrap.com/css/#type-body-copy
	 */
	public static function p($text = null) {
		return new Bootstrap\Paragraph($text);
	}
	
	/**
	 * New form
	 * @param string $action
	 * @return \Bootstrap\Form
	 * @link http://getbootstrap.com/css/#forms
	 */
	public static function form($action = null) {
		return new Bootstrap\Form($action);
	}
	
	/**
	 * New hidden field
	 * @param string $name
	 * @param mixed $value
	 * @return \Bootstrap\Input\Hidden
	 * @link http://getbootstrap.com/css/#forms
	 */
	public static function hiddenfield($name, $value = null) {
		return new Bootstrap\Input\Hidden($name, $value);
	}
	
	/**
	 * New input type text field
	 * @param string $name
	 * @param string $value
	 * @param string $label
	 * @return \Bootstrap\Input\Textfield
	 * @link http://getbootstrap.com/css/#forms
	 */
	public static function textfield($name, $value = null, $label = null) {
		return new Bootstrap\Input\Textfield($name, $value, $label);
	}
	
	/**
	 * New input type number field
	 * @param string $name
	 * @param string $value
	 * @param string $label
	 * @return \Bootstrap\Input\Numberfield
	 * @link http://getbootstrap.com/css/#forms
	 */
	public static function numberfield($name, $value = null, $label = null) {
		return new Bootstrap\Input\Numberfield($name, $value, $label);
	}
	
	/**
	 * New checkbox
	 * @param string $name
	 * @param string $value
	 * @param string $label
	 * @param bool $checked
	 * @return \Bootstrap\Input\Checkbox
	 */
	public static function checkbox($name, $value = null, $label = null, $checked = null) {
		return new Bootstrap\Input\Checkbox($name, $value, $label, $checked);
	}
	
	/**
	 * New checkbox
	 * @param string $name
	 * @param array $options
	 * @param string $label
	 * @param string $value
	 * @return \Bootstrap\Input\Radio
	 */
	public static function radio($name, array $options, $label = null, $value = null) {
		return new Bootstrap\Input\Radio($name, $options, $label, $value);
	}
	
	/**
	 * New textarea field
	 * @param string $name
	 * @param string $value
	 * @param string $label
	 * @return \Bootstrap\Input\Textarea
	 */
	public static function textarea($name, $value = null, $label = null) {
		return new Bootstrap\Input\Textarea($name, $value, $label);
	}
	
	/**
	 * New select field
	 * @param string $name
	 * @param string $label
	 * @param array $values
	 * @param string $value
	 * @return \Bootstrap\Input\Select
	 */
	public static function select($name, $label = null, array $values = array(), $value = null) {
		return new Bootstrap\Input\Select($name, $label, $values, $value);
	}
	
	/**
	 * New button
	 * @param string $text
	 * @return \Bootstrap\Button
	 * @link http://getbootstrap.com/css/#buttons
	 */
	public static function button($text) {
		return new Bootstrap\Button($text);
	}
	
	/**
	 * New button group
	 * @return \Bootstrap\ButtonGroup
	 * @link http://getbootstrap.com/css/#btn-groups
	 */
	public static function buttonGroup() {
		return new Bootstrap\ButtonGroup();
	}
	
	/**
	 * New link that can look like button
	 * @param string $text
	 * @param string $url
	 * @return \Bootstrap\Anchor
	 * @link http://getbootstrap.com/css/#buttons-active
	 */
	public static function anchor($text, $url) {
		return new Bootstrap\Anchor($text, $url);
	}
	
	/**
	 * New panel
	 * @param string $title
	 * @param string $content
	 * @param string $footer
	 * @return \Bootstrap\Panel
	 * @link http://getbootstrap.com/components/#panels
	 */
	public static function panel($title = null, $content = null, $footer = null) {
		return new Bootstrap\Panel($title, $content, $footer);
	}
	
	/**
	 * New list group
	 * @return \Bootstrap\ListGroup
	 * @link http://getbootstrap.com/components/#list-group
	 */
	public static function listGroup() {
		return new Bootstrap\ListGroup();
	}
	
	/**
	 * New nav
	 * @return \Bootstrap\Nav
	 * @link http://getbootstrap.com/components/#nav
	 */
	public static function nav() {
		return new Bootstrap\Nav();
	}
	
	/**
	 * New navbar
	 * @return \Bootstrap\Navbar
	 * @link http://getbootstrap.com/components/#navbar
	 */
	public static function navbar() {
		return new Bootstrap\Navbar();
	}
	
	/**
	 * New blockquote
	 * @param string $text
	 * @return \Bootstrap\Blockquote
	 * @link http://getbootstrap.com/css/#type-blockquotes
	 */
	public static function blockquote($text = null) {
		return new Bootstrap\Blockquote($text);
	}
	
	/**
	 * New table
	 * @return \Bootstrap\Table
	 * @link http://getbootstrap.com/css/#tables
	 */
	public static function table() {
		return new Bootstrap\Table();
	}
	
	/**
	 * Get the icon HTML
	 * @param string $icon
	 * @param string $color
	 * @param int $size
	 * @return Bootstrap\Icon
	 * @link http://getbootstrap.com/components/#glyphicons
	 */
	public static function icon($icon, $color = null, $size = null) {
		return new Bootstrap\Icon($icon, $color, $size);
	}
	
	/**
	 * The label text
	 * @param string $text
	 * @return \Bootstrap\Label
	 * @link http://getbootstrap.com/components/#labels
	 */
	public static function label($text) {
		return new Bootstrap\Label($text);
	}
	
	/**
	 * New alert object
	 * @param mixed $element
	 * @return \Bootstrap\Alert
	 */
	public static function alert($element = null) {
		return new Bootstrap\Alert($element);
	}
	
	/**
	 * New breadcrumbs element
	 * @return \Bootstrap\Breadcrumbs
	 * @link http://getbootstrap.com/components/#breadcrumbs
	 */
	public static function breadcrumbs() {
		return new Bootstrap\Breadcrumbs();
	}
	
	/**
	 * New image
	 * @param string $src
	 * @return \Bootstrap\Image
	 */
	public static function image($src = null) {
		return new Bootstrap\Image($src);
	}
}