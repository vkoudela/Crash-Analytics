<?php namespace Bootstrap;

class Paragraph extends NestableElement {
	
	/**
	 * Color of text
	 * @var string
	 */
	private $color = null;
	
	public function __construct($text = null) {
		if ($text !== null) {
			$this->add($text);
		}
	}
	
	/**
	 * Set the text in this element
	 * @param string $text
	 * @return \Bootstrap\Paragraph
	 */
	public function text($text) {
		$this->removeElements()->add($text);
		return $this;
	}
	
	/**
	 * Set the color of text
	 * @param string $color
	 * @return \Bootstrap\Paragraph
	 */
	public function color($color) {
		if (isset(static::$colors[$color])) {
			$this->addClass('text-' . static::$colors[$color]);
			$this->color = $color;
		}
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$html = "<p{$this->idAttr()}{$this->classAttr()}{$this->getAttributes()}>\n";
		$html .= "\t{$this->getElementsHtml()}\n";
		$html .= "</p>\n";
		
		return $html;
	}
}