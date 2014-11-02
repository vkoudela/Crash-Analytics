<?php namespace Bootstrap;

class Row extends NestableElement {

	/**
	 * Array of columns in this row
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Add column to this row
	 * @param int $size
	 * @param mixed $element
	 * @param int $offset
	 * @return \Bootstrap\Row
	 */
	public function add($size, $element, $offset = null) {
		$this->columns[] = array(
			'size' => $size,
			'element' => $element,
			'offset' => $offset
		);
		
		$this->elements[] = $element;

		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$html = "<div{$this->classAttr('row')}{$this->idAttr()}{$this->getAttributes()}>\n";

		foreach ($this->columns as $column) {
			$size = $column['size'];
			$offset = ($column['offset'] !== null) ? " col-md-offset-{$column['offset']}" : '';
			$element = is_array($column['element']) ? implode("\n", $column['element']) : $column['element'];
			$html .= "\t<div class=\"col-md-{$size}{$offset}\">\n\t\t{$element}\n\t</div>\n";
		}

		$html .= '</div>';

		return $html;
	}
}
