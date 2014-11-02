<?php namespace Bootstrap;
/**
 * Button class. Buttons are rendered as <button>text</button>.
 *
 */
class Button extends HtmlElement {

	/**
	 * The button's text.
	 * @var string
	 */
	protected $text = null;

	/**
	 * Default button type
	 * @var string
	 */
	protected $type = 'button';
	
	/**
	 * Available button's sizes. The keys of this array are possible values you can pass to color() method
	 * @var array
	 */
	private $sizes = array(
		'large' => 'lg',
		'lg' => 'lg',
		'small' => 'sm',
		'sm' => 'sm',
		'extra-small' => 'xs',
		'xs' => 'xs'
	);

	public function __construct($text) {
		$this->text = $text;
	}

	/**
	 * Update the button's text
	 * @param string $text
	 * @return \Bootstrap\Button
	 */
	public function text($text) {
		$this->text = $text;
		return $this;
	}

	/**
	 * Set the button's name
	 * @param string $name
	 * @return \Bootstrap\Button
	 */
	public function name($name) {
		$this->setAttribute('name', $name);
		return $this;
	}

	/**
	 * Set the button's type
	 * @param string $type submit, button or reset; 'button' is by default and doesn't have to be set
	 * @return \Bootstrap\Button
	 */
	public function type($type) {
		$this->type = $type;
		return $this;
	}

	/**
	 * Set the button background color
	 * @param string $color
	 * @return \Bootstrap\Button
	 */
	public function color($color) {
		if (isset(static::$colors[$color])) {
			$this->addClass('btn-' . static::$colors[$color]);
		}
		return $this;
	}

	/**
	 * Render button as link
	 * @return \Bootstrap\Button
	 */
	public function asLink() {
		return $this->addClass('btn-link');
	}
	
	/**
	 * Make the button 100% wide
	 * @return \Bootstrap\Button
	 * @link http://getbootstrap.com/css/#buttons
	 */
	public function asBlock() {
		return $this->addClass('btn-block');
	}

	/**
	 * Set the button's size
	 * @param string $size
	 * @return \Bootstrap\Button
	 * @link http://getbootstrap.com/css/#buttons
	 */
	public function size($size) {
		if (isset($this->sizes[$size])) {
			return $this->addClass("btn-{$this->sizes[$size]}");
		}
		return $this;
	}
	
	/**
	 * Set button to disabled state
	 * @return \Bootstrap\Button
	 */
	public function disabled() {
		return $this->setAttribute('disabled', 'disabled');
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		return "<button type=\"{$this->type}\"{$this->idAttr()}{$this->classAttr('btn')}{$this->getAttributes()}>{$this->text}</button>";
	}
}