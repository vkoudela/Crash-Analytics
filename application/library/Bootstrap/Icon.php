<?php namespace Bootstrap;

class Icon extends HtmlElement {

	/**
	 * @var string
	 */
	protected $color = null;
	
	/**
	 * The icon size
	 * @var string
	 */
	protected $size = null;
	
	/**
	 * The icon set
	 * @var string
	 */
	protected $icon = null;
	
	/**
	 * Construct the icon object
	 * @param string $icon
	 */
	public function __construct($icon, $color = null, $size = null) {
		$this->icon = $icon;
		$this->color = $color;
		$this->size = $size;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$style = '';
		
		if ($this->color !== null) {
			switch($this->color) {
				case 'red': $color = '#d2322d'; break;
				case 'blue': $color = '#428bca'; break;
				case 'green': $color = '#5cb85c'; break;
			}
			$style .= "color: {$color}; ";
		}
		
		if ($this->size !== null) {
			$style .= "font-size: {$this->size}px; ";
		}
		
		if (strlen($style) > 0) {
			$style = ' style="' . substr($style, 0, -2) . '"';
		}
		
		return "<span{$this->classAttr("glyphicon glyphicon-{$this->icon}")}{$this->getAttributes()}{$style}></span>";
	}
}
