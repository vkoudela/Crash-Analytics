<?php namespace Bootstrap;

class Nav extends HtmlElement {
	
	protected $elements = array();
	
	protected $style = 'tabs';
	
	protected $active = 0;
	
	public function stacked() {
		$this->addClass('nav-stacked');
		return $this;
	}
	
	public function justify() {
		$this->addClass('nav-justified');
		return $this;
	}
	
	public function asPills() {
		$this->style = 'pills';
		return $this;
	}
	
	public function active($whichOne) {
		$this->active = $whichOne;
		return $this;
	}
	
	public function addLink($title, $content) {
		$this->elements[] = array(
			'type' => 'tab',
			'title' => $title,
			'content' => $content
		);
		return $this;
	}
	
	/**
	 * Get the count of elements
	 * @return int
	 */
	public function count() {
		return sizeof($this->elements);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$this->addClass("nav-{$this->style}");
		$html = "<ul{$this->classAttr('nav')}{$this->idAttr()}{$this->getAttributes()}>";
		
		foreach ($this->elements as $index => $element) {
			switch($element['type']) {
				case 'tab':
					$class = ($this->active !== null && $index == $this->active) ? ' class="active"' : '';
					$a = "<a href=\"#{$this->getId()}-{$index}\" data-toggle=\"tab\">{$element['title']}</a>";
					$html .= "\t<li{$class}>{$a}</li>\n";
					break;
			}
		}
		
		$html .= '</ul>';
		
		$html .= "\n\n<div class=\"tab-content x-nav-tab-content\" id=\"{$this->getId()}Content\">\n";
		foreach ($this->elements as $index => $element) {
			$active = ($this->active !== null && $index == $this->active) ? 'active ' : '';
			$html .= "\t<div class=\"tab-pane fade {$active}in\" id=\"{$this->getId()}-{$index}\">\n\t\t{$element['content']}\n\t</div>\n";
		}
		$html .= '</div>';
		
		return $html;
	}
}