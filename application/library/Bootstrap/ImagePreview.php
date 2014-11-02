<?php namespace Bootstrap;

class ImagePreview extends HtmlElement {
	
	/**
	 * The URL to the picture
	 * @var string
	 */
	protected $imageUrl = null;
	
	/**
	 * The array of additional elements
	 * @var array
	 */
	protected $elements = array();
	
	/**
	 * Set the image URL
	 * @param string $url
	 * @return \Bootstrap\ImagePreview
	 */
	public function imageUrl($url) {
		$this->imageUrl = $url;
		return $this;
	}
	
	/**
	 * Add element to the picture preview
	 * @param mixed $element
	 * @return \Bootstrap\ImagePreview
	 */
	public function add($element) {
		$this->elements[] = $element;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$html = "<div{$this->getCss('x-image-preview')} id=\"{$this->getId()}\"{$this->getAttributes()} style=\"background-image: url(" . $this->imageUrl . '?t=' . time() . ");\">";

		foreach ($this->elements as $element) {
			$html .= $element;
		}
		
		$html .= '</div>';
		
		return $html;
	}
}