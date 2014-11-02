<?php namespace Bootstrap\Input;

abstract class AbstractElement extends \Bootstrap\HtmlElement {

	/**
	 * The name of element
	 * @var string
	 */
	protected $name = null;

	/**
	 * Set the element's name
	 * @param string $name
	 * @return \Bootstrap\Input\AbstractElement
	 */
	public function name($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * Get the element's name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Make element disabled
	 * @return \Bootstrap\Input\AbstractElement
	 */
	public function disabled() {
		return $this->setAttribute('disabled', 'disabled')->addClass('x-disabled');
	}

}