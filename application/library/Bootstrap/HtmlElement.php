<?php namespace Bootstrap;

abstract class HtmlElement {

	/**
	 * Global counter of Bootstrap elements
	 * @var int
	 */
	private static $fieldCounter = 0;

	/**
	 * The ID in HTML
	 * @var string
	 */
	private $id = null;

	/**
	 * Array of additional attributes
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * The array of additional css classes
	 * @var array
	 */
	protected $cssClasses = array();
	
	/**
	 * The array of any information that this object is carring
	 * @var array
	 */
	protected $localData = array();
	
	/**
	 * Possible colors of element
	 * @var array
	 */
	protected static $colors = array(
		'default' => 'default',
		'primary' => 'primary',
		'blue' => 'primary',
		'success' => 'success',
		'green' => 'success',
		'info' => 'info',
		'lightblue' => 'info',
		'warning' => 'warning',
		'orange' => 'warning',
		'danger' => 'danger',
		'red' => 'danger'
	);

	/**
	 * Set the custom HTML ID attribute
	 * @param string $id
	 * @return \Bootstrap\HtmlElement
	 */
	public function id($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Get the ID. If ID is not set, it will be generated
	 * @return string
	 */
	public function getId() {
		if ($this->id === null) {
			$this->id = "el_" . (++self::$fieldCounter);
		}

		return $this->id;
	}
	
	/**
	 * Get the ID attribute
	 * @return string
	 */
	protected function idAttr() {
		return " id=\"{$this->getId()}\"";
	}

	/**
	 * Get the global ID attribute
	 * @return string
	 */
	public static function getGlobalId() {
		return "el_" . (++self::$fieldCounter);
	}

	/**
	 * Add one or more CSS classes
	 * @param string|array $cssClass
	 * @return \Bootstrap\HtmlElement
	 */
	public function addClass($cssClass) {
		$this->cssClasses[] = is_array($cssClass) ? implode(' ', $cssClass) : $cssClass;
		return $this;
	}

	/**
	 * Remove CSS class
	 * @param string $class
	 * @return \Bootstrap\HtmlElement
	 */
	public function removeClass($class) {
		$classes = array_flip($this->cssClasses);
		if (isset($classes[$class])) {
			unset($classes[$class]);
			$this->cssClasses = array_flip($classes);
		}
		return $this;
	}

	/**
	 * Get the value of class attribute
	 * @param string $default
	 * @return string
	 */
	protected function getClasses($default = null) {
		if ($default !== null) {
			$default = is_array($default)
				? implode(' ', $default)
				: $default;

			$default .= ' ';
		} else {
			$default = '';
		}

		return trim($default . implode(' ', $this->cssClasses));
	}

	/**
	 * Get the complete class attribute with name and value
	 * @param string $defaultClass
	 * @return string
	 */
	protected function classAttr($defaultClass = null) {
		$classes = $this->getClasses($defaultClass);
		return strlen($classes) ? " class=\"{$classes}\"" : '';
	}

	/**
	 * Add additional attribute
	 * @param string $name
	 * @param mixed $value
	 * @return \Bootstrap\HtmlElement
	 */
	public function setAttribute($name, $value) {
		$this->attributes[$name] = $value;
		return $this;
	}

	/**
	 * Set the data atribute to this element
	 * @param string $name
	 * @param string|mixed $value
	 * @return \Bootstrap\HtmlElement
	 */
	public function data($name, $value) {
		return $this->setAttribute("data-{$name}", $value);
	}

	/**
	 * Get attributes with their values
	 * @return string
	 */
	protected function getAttributes() {
		$s = '';
		foreach ($this->attributes as $name => $value) {
			$value = str_replace('"', '&quot;', $value);
			$s .= " {$name}=\"{$value}\"";
		}

		return $s;
	}
	
	/**
	 * Set the local data for any kind of information holder
	 * @param string $key
	 * @param mixed $value
	 * @return \Bootstrap\HtmlElement
	 */
	public function setLocalData($key, $value) {
		$this->localData[$key] = $value;
		return $this;
	}
	
	/**
	 * Get the local data previously stored for some reason
	 * @param string $key
	 * @return mixed
	 */
	public function getLocalData($key) {
		return $this->localData[$key];
	}
	
	/**
	 * Is there local data set
	 * @param string $key
	 * @return boolean
	 */
	public function hasLocalData($key) {
		return isset($this->localData[$key]);
	}

	abstract public function getHtml();

	public function __toString() {
		return $this->getHtml();
	}
}