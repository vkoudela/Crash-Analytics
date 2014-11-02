<?php namespace Bootstrap\Input;

class Hidden extends AbstractElement {

	protected $value = null;

	public function __construct($name, $value = null) {
		$this->name = $name;
		$this->value = $value;
	}

	public function value($value) {
		$this->value = $value;
		return $this;
	}

	public function getValue() {
		return $this->value;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		return "\t<input type=\"hidden\" name=\"{$this->name}\" value=\"{$this->value}\">\n";
	}
}