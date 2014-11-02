<?php namespace Bootstrap;

class ButtonGroup extends NestableElement {

	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$html = "<div{$this->classAttr('btn-group')}{$this->idAttr()}{$this->getAttributes()}>";

		$html .= $this->getElementsHtml();

		$html .= '</div>';

		return $html;
	}
}
