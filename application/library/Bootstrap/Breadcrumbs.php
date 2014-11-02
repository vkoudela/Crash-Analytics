<?php namespace Bootstrap;
/**
 * @author vkoudela
 * @link http://getbootstrap.com/components/#breadcrumbs
 *
 */
class Breadcrumbs extends HtmlElement {
	
	/**
	 * Items in breadcrumbs
	 * @var array
	 */
	protected $items = array();
	
	/**
	 * Which item is selected
	 * @var int
	 */
	protected $selected = null;
	
	/**
	 * Add element into breadcrumb
	 * @param string $text
	 * @param string $href
	 * @param string $target
	 * @return \Bootstrap\Breadcrumbs
	 */
	public function add($text, $href = null, $target = null) {
		$this->items[] = array(
			'text' => $text,
			'href' => $href,
			'target' => $target
		);
		
		return $this;
	}
	
	/**
	 * Set active element
	 * @param int $selected
	 * @return \Bootstrap\Breadcrumbs
	 */
	public function active($selected) {
		$this->selected = $selected;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$html = "<ol{$this->classAttr('breadcrumb')}{$this->idAttr()}>\n";
		
		foreach ($this->items as $index => $item) {
			$active = ($this->selected === $index) ? ' class="active"' : '';
			
			$href = ($item['href'] !== null) ? $item['href'] : '#';
			$target = ($item['target'] !== null) ? " target=\"{$item['target']}\"" : '';
			
			$html .= "\t<li{$active}><a href=\"{$href}\"{$target}>{$item['text']}</a></li>\n";
		}
		
		$html .= "</ol>\n";
		return $html;
	}
	
}