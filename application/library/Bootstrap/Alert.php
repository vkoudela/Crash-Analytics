<?php namespace Bootstrap;

class Alert extends HtmlElement {
	
	protected $dismissable = false;
	
	protected $elements = array();
	
	public function __construct($element = null) {
		$this->addClass('alert');
		
		if ($element !== null) {
			$this->elements[] = $element;
		}
	}
	
	public function add($element) {
		if (is_array($element)) {
			foreach ($element as $e) {
				$this->elements[] = $e;
			}
		} else {
			$this->elements[] = $element;
		}
		return $this;
	}
	
	public function color($color) {
		if (isset(static::$colors[$color])) {
			$this->addClass('alert-' . static::$colors[$color]);
		}
		return $this;
	}
	
	public function dismissable() {
		$this->dismissable = true;
		return $this;
	}
	
	public function getHtml() {
		if ($this->dismissable) {
			$dismissable = '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
			$this->addClass('alert-dismissable');
		} else {
			$dismissable = '';
		}

		$elements = implode("\n\t\t\t", $this->elements);
		$html = "
		<div class=\"{$this->getClasses()}\"{$this->getAttributes()} id=\"{$this->getId()}\">
			{$dismissable}
			{$elements}
		</div>
		";
		
		return $html;
	}
	
}