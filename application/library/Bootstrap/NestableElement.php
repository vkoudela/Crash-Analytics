<?php namespace Bootstrap;

abstract class NestableElement extends HtmlElement {

	/**
	 * Array of nested elements
	 * @var array
	 */
	protected $elements = array();
	
	/**
	 * Add nested element
	 * @param mixed $element
	 * @return \Bootstrap\NestableElement
	 */
	public function add($element) {
		if (is_array($element)) {
			foreach ($element as $e) {
				$this->elements[] = $e;
			}
		} else {
			$this->elements[] = $element;
		}
		return $this;
	}
	
	/**
	 * Get the array of elements
	 * @return array
	 */
	public function getElements() {
		return $this->elements;
	}
	
	/**
	 * Remove all added elements
	 * @return \Bootstrap\NestableElement
	 */
	public function removeElements() {
		$this->elements = array();
		return $this;
	}
	
	/**
	 * Get the elements HTML
	 * @return string
	 */
	public function getElementsHtml() {
		return implode("\n", $this->elements);
	}

}