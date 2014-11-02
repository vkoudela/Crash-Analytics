<?php namespace Bootstrap\Input;

class Radio extends AbstractElement {

	/**
	 * The element's value
	 * @var mixed
	 */
	protected $value = null;
	
	/**
	 * The array of options
	 * @var array
	 */
	protected $options = null;

	/**
	 * The element's label
	 * @var string
	 */
	protected $label = null;

	/**
	 * The label width
	 * @var string
	 */
	protected $labelWidth = null;

	/**
	 * Construct the element
	 * @param string $name
	 * @param array $options
	 * @param string $label
	 * @param string $value
	 */
	public function __construct($name, array $options, $label = null, $value = null) {
		$this->name = $name;
		$this->options = $options;
		$this->label = $label;
		$this->value = $value;
	}

	/**
	 * Set the label
	 * @param string $label
	 * @return \Bootstrap\Input\Radio
	 */
	public function label($label) {
		$this->label = $label;
		return $this;
	}

	/**
	 * Get the label
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * The bootstrap width, between 1 and 11
	 * @param  integer $width
	 * @return  \Bootstrap\Input\Radio
	 */
	public function labelWidth($width) {
		$this->labelWidth = $width;
		return $this;
	}

	/**
	 * Get the label width
	 * @return string
	 */
	public function getLabelWidth() {
		return $this->labelWidth;
	}

	/**
	 * Set the element's value
	 * @param mixed $value
	 * @return \Bootstrap\Input\Radio
	 */
	public function value($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * Get the element's value
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Update the options array
	 * @param array $options
	 * @return \Bootstrap\Input\Radio
	 */
	public function options(array $options) {
		$this->options = $options;
		return $this;
	}
	
	/**
	 * Get the HTML for input
	 * @return string
	 */
	protected function getInputHtml() {
		$html = '';
		
		if ($this->labelWidth !== null) {
			$html .= "\t<div class=\"col-sm-" . (12 - $this->labelWidth) . "\">\n";
		}
				
		foreach ($this->options as $value => $text) {
			$html .= "\t\t<div class=\"radio\"{$this->idAttr()}>\n";
			$html .= "\t\t\t<label>";
			$html .= "<input type=\"radio\" name=\"{$this->name}\" value=\"{$value}\"";
			if ($value == $this->value) {
				$html .= ' checked';
			}
			
			$html .= "/> {$text}";
			$html .= "</label>\n";
			$html .= "\t\t</div>\n";
		}
		
		if ($this->labelWidth !== null) {
			$html .= "\t</div>\n";
		}
				
		return $html;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$html = '<div class="form-group">';

		if ($this->label !== null && $this->labelWidth === null) {
			$html .= "\n\t<label class=\"control-label\">{$this->label}</label>\n";
		} else if ($this->label !== null || $this->labelWidth !== null) {
			$html .= "\n\t<label class=\"col-sm-{$this->labelWidth} control-label\">{$this->label}</label>\n";
		}

		$html .= $this->getInputHtml();
		$html .= "</div>\n";

		return $html;
	}
}