<?php namespace Highcharts;

abstract class AbstractChart {
	
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
	 * The container minimum width
	 * @var int
	 */
	private $minWidth = 200;
	
	/**
	 * The container height
	 * @var int
	 */
	private $height = 250;
	
	/**
	 * The options array
	 * @var array
	 */
	private $options = array();
	
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
			$this->id = "hc_" . (++self::$fieldCounter);
		}
	
		return $this->id;
	}
	
	/**
	* Get the global ID attribute
	* @return string
	*/
	public static function getGlobalId() {
		return "hc_" . (++self::$fieldCounter);
	}
	
	/**
	 * Set the element height
	 * @param int $height
	 * @return \Highcharts\Element
	 */
	public function height($height) {
		$this->height = $height;
		return $this;
	}
	
	/**
	 * Set the minimum width
	 * @param int $minWidth
	 * @return \Highcharts\Element
	 */
	public function minWidth($minWidth) {
		$this->minWidth = $minWidth;
		return $this;
	}
	
	/**
	 * Set the options key
	 * @param string $key
	 * @param mixed $value
	 * @return \Highcharts\AbstractChart
	 */
	protected function set($key, $value) {
		$this->options[$key] = $value;
		return $this;
	}
	
	/**
	 * Does given key exists
	 * @param string $key
	 * @return bool
	 */
	protected function has($key) {
		return isset($this->options[$key]);
	}
	
	/**
	 * Get the key's value
	 * @param string $key
	 * @return mixed
	 */
	protected function get($key) {
		return $this->options[$key];
	}
	
	/**
	 * Remove the given key
	 * @param string $key
	 * @return \Highcharts\AbstractChart
	 */
	protected function remove($key) {
		if ($this->has($key)) {
			unset($this->options[$key]);
		}
		return $this;
	}
	
	/**
	 * Get the array of options
	 * @return array
	 */
	protected function getOptions() {
		return $this->options;
	}
	
	/**
	 * Get the HTML
	 * @return string
	 */
	protected function getHtml() {
		$id = $this->getId();
		$options = json_encode($this->getOptions());
		
		$html = "<div id=\"{$id}\" style=\"min-width: {$this->minWidth}px; height: {$this->height}px; margin: 0 auto;\"></div>\n";
		
		$js = "<script type=\"text/javascript\">\n";
		$js .= "\t$(function () {\n";
		$js .= "\t\t\$('#{$id}').highcharts({$options});\n";
		$js .= "\t});\n";
		$js .= '</script>';
		
		return $html . $js;
	}
	
	public function __toString() {
		return $this->getHtml();
	}
	
}