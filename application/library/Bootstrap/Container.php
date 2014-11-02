<?php namespace Bootstrap;

class Container extends HtmlElement {

	/**
	 * The array of nested elements
	 * @var array
	 */
	protected $elements = array();

	/**
	 * Add element into container
	 * @param Row $element
	 * @return \Bootstrap\Container
	 */
	public function add(Row $element) {
		$this->elements[] = $element;
		return $this;
	}
	
	/**
	 * Add row of size 12 and add this element to it
	 * @param mixed $element
	 * @return \Bootstrap\Container
	 */
	public function addElement($element) {
		$row = new Row();
		$row->add(12, $element);
		return $this->add($row);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$html = "<div{$this->idAttr()}{$this->classAttr('container')}{$this->getAttributes()}>\n";
		$html .= implode("\n", $this->elements);
		$html .= '</div>';

		return $html;
	}
}