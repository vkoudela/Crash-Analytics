<?php namespace Bootstrap;

class Heading extends NestableElement {
	
	/**
	 * The element color
	 * @var string
	 */
	private $color = null;
	
	/**
	 * The heading size (1-6)
	 * @var int
	 */
	private $size = null;
	
	/**
	 * Construct the element
	 * @param int $size
	 * @param string $text
	 * @throws Exception
	 */
	public function __construct($size, $text = null) {
		if ($size < 1 || $size > 6) {
			throw new Exception('Can not construct heading element with restricted size');
		}
		
		$this->size = $size;
		
		if ($text !== null) {
			$this->add($text);
		}
	}
	
	/**
	 * Set the text
	 * @param string $text
	 * @return \Bootstrap\Heading
	 */
	public function text($text) {
		return $this->removeElements()->add($text);
	}
	
	/**
	 * Set the color
	 * @param string $color
	 * @return \Bootstrap\Heading
	 */
	public function color($color) {
		if (isset(static::$colors[$color])) {
			$this->addClass('text-' . static::$colors[$color]);
			$this->color = $color;
		}
		return $this;
	}
	
	/**
	 * Set secondary text
	 * @param string $text
	 * @return \Bootstrap\Heading
	 */
	public function secondaryText($text) {
		return $this->add(" <small>{$text}</small>");
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		return "<h{$this->size}{$this->idAttr()}{$this->classAttr()}{$this->getAttributes()}>{$this->getElementsHtml()}</h{$this->size}>\n";
	}
}