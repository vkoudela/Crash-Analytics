<?php namespace Bootstrap\Row;

class Column extends \Bootstrap\HtmlElement {

	protected $elements = array();

	public static function start($size) {
		$self = new self();
		return "<div class=\"col-md-{$size}\" id=\"{$self->getId()}\">";
	}

	public static function end() {
		return '</div>';
	}

	public function add($element) {
		$this->elements[] = $element;
	}

	public function getHtml() {
		$html = "<div class=\"col-md-{$size}\" id=\"{$this->getId()}\">";
		foreach ($this->elements as $element) {
			$html .= $element;
		}
		$html .= '</div>';
		return $html;
	}

}