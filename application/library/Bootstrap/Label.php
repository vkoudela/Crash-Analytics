<?php namespace Bootstrap;

class Label extends HtmlElement {
	
	/**
	 * The text of label
	 * @var string
	 */
	protected $text = null;
	
	/**
	 * The bootstrap color
	 * @var string
	 */
	private $color = null;
	
	/**
	 * Construct the label object with text
	 * @param string $text
	 */
	public function __construct($text) {
		$this->text($text);
	}
	
	/**
	 * Set label text
	 * @param string $text
	 * @return \Bootstrap\Panel
	 */
	public function text($text) {
		$this->text = $text;
		return $this;
	}
	
	/**
	 * Set the bootstrap color.
	 * @param string $color
	 * @return \Bootstrap\Panel
	 */
	public function color($color) {
		if (isset(static::$colors[$color])) {
			$this->addClass('label-' . static::$colors[$color]);
			$this->color = $color;
		}
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		return "<span{$this->idAttr()}{$this->classAttr('label')}{$this->getAttributes()}>{$this->text}</span>";
	}
	
}