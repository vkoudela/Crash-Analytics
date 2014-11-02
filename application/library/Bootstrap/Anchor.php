<?php namespace Bootstrap;
/**
 * Link is simple <a> tag that might look like button if you call asButton() method.
 * @author vkoudela
 *
 */
class Anchor extends HtmlElement {

	/**
	 * Anchor text
	 * @var string
	 */
	protected $text = null;

	/**
	 * URL where it goes
	 * @var string
	 */
	protected $url = null;

	/**
	 * Available sizes
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

	/**
	 * Construct the object
	 * @param string $text
	 * @param string $url
	 */
	public function __construct($text, $url) {
		$this->text = $text;
		$this->url = $url;
		$this->addClass('btn')->addClass('btn-link');
	}

	/**
	 * Update text
	 * @param string $text
	 * @return \Bootstrap\Anchor
	 */
	public function text($text) {
		$this->text = $text;
		return $this;
	}

	/**
	 * Set button color
	 * @param string $color
	 * @return \Bootstrap\Anchor
	 * @throws \Bootstrap\Exception
	 */
	public function color($color) {
		if (isset(static::$colors[$color])) {
			$this->addClass('btn-' . static::$colors[$color]);
		} else {
			throw new Exception('Can not set color to ' . $color);
		}
		return $this;
	}

	/**
	 * Set size
	 * @param string $size 'lg', 'sm', 'xs' or 'large', 'small', 'extra-small'
	 * @return \Bootstrap\Anchor
	 * @link http://getbootstrap.com/css/#buttons-sizes
	 * @throws \Bootstrap\Exception
	 */
	public function size($size) {
		if (isset($this->sizes[$size])) {
			return $this->addClass("btn-{$this->sizes[$size]}");
		} else {
			throw new Exception('Can not set size to ' . $size);
		}
		return $this;
	}

	/**
	 * Render this link as button
	 * @return \Bootstrap\Anchor
	 */
	public function asButton() {
		return $this->removeClass('btn-link');
	}

	/**
	 * Set the link target
	 * @param string $target '_blank' or '_top' or something like that
	 * @return \Bootstrap\Anchor
	 */
	public function target($target) {
		return $this->setAttribute('target', $target);
	}

	/**
	 * Set the link title
	 * @param string $title
	 * @return \Bootstrap\Anchor
	 */
	public function title($title) {
		return $this->setAttribute('title', $title);
	}
	
	/**
	 * Add prompt message after user clicks on link.
	 * @param string $promptText
	 * @return \Bootstrap\Anchor
	 */
	public function promptText($promptText) {
		$promptText = str_replace('\'', '\\\'', $promptText);
		$promptText = str_replace('"', '&quot;', $promptText);
		return $this->setAttribute('onclick', "return confirm('{$promptText}')");
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$html = "<a href=\"{$this->url}\"{$this->idAttr()}{$this->classAttr()}{$this->getAttributes()}>{$this->text}</a>";
		return $html;
	}
}