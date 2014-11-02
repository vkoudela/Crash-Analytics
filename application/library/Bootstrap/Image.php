<?php namespace Bootstrap;
/**
 * The image.
 * @author vkoudela
 * @link http://getbootstrap.com/css/#images
 *
 */
class Image extends HtmlElement {
	
	protected $type = 'rounded';
	
	/**
	 * Construct the object
	 * @param string $src
	 */
	public function __construct($src = null) {
		if ($src !== null) {
			$this->src($src);
		}
	}
	
	/**
	 * Render as circle
	 * @return \Bootstrap\Image
	 */
	public function circle() {
		$this->type = 'circle';
		return $this;
	}
	
	/**
	 * Render as thumbnail
	 * @return \Bootstrap\Image
	 */
	public function thumbnail() {
		$this->type = 'thumbnail';
		return $this;
	}
	
	/**
	 * Set image src
	 * @param string $path
	 * @return \Bootstrap\Image
	 */
	public function src($path) {
		return $this->setAttribute('src', $path);
	}
	
	/**
	 * Set image alternative text
	 * @param string $text
	 * @return \Bootstrap\Image
	 */
	public function alt($text) {
		return $this->setAttribute('alt', $text);
	}
	
	/**
	 * Make image responsive
	 * @return \Bootstrap\Image
	 */
	public function responsive() {
		return $this->addClass('img-responsive');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$this->addClass('img-' . $this->type);
		
		$html = "
		<img{$this->classAttr()}{$this->idAttr()}{$this->getAttributes()}>
		";
		
		return $html;
	}
}