<?php namespace Bootstrap\Input;
/**
 * Checkbox field
 * @author vkoudela
 *
 */
class Checkbox extends AbstractElement {

	/**
	 * The value of checkbox
	 * @var string
	 */
	protected $value = null;
	
	/**
	 * Is it checked or not
	 * @var bool
	 */
	protected $checked = false;

	/**
	 * Field label
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
	 * @param string $value
	 * @param string $label
	 */
	public function __construct($name, $value = null, $label = null, $checked = null) {
		$this->name = $name;
		$this->value = $value;
		$this->label = $label;
		
		if ($checked !== null) {
			$this->checked = (bool) $checked;
		}
	}

	/**
	 * Set the field's label
	 * @param string $label
	 * @return \Bootstrap\Input\Checkbox
	 */
	public function label($label) {
		$this->label = $label;
		return $this;
	}

	/**
	 * Get the field's label
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * The bootstrap width, between 1 and 11
	 * @param  integer $width
	 * @return  \Bootstrap\Input\Text
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
	 * Set the value
	 * @param string $value
	 * @return \Bootstrap\Input\Checkbox
	 */
	public function value($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * Get the value
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Is checkbox checked or not
	 * @param bool $checked
	 * @return \Bootstrap\Input\Checkbox
	 */
	public function checked($checked = true) {
		$this->checked = $checked;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$html = '<div class="form-group">';

		if ($this->label !== null && $this->labelWidth === null) {
			$html .= "\n\t<div class=\"control-label\"></div>\n";
		} else if ($this->label !== null || $this->labelWidth !== null) {
			$html .= "\n\t<div class=\"col-sm-{$this->labelWidth} control-label\"></div>\n";
		}

		if ($this->name !== null) {
			$this->setAttribute('name', $this->name);
		}

		if ($this->value !== null) {
			$this->setAttribute('value', $this->value);
		}
		
		if ($this->checked) {
			$this->setAttribute('checked', 'checked');
		}

		if ($this->labelWidth === null) {
			$html .= "\t<div class=\"checkbox\">\n";
			$html .= "\t\t<label>\n";
			$html .= "\t\t\t<input type=\"checkbox\"{$this->classAttr()}{$this->idAttr()}{$this->getAttributes()}>\n";
			$html .= "\t\t{$this->label}</label>\n";
			$html .= "\t\t</div>\n";
		} else {
			$html .= "\t<div class=\"col-sm-" . (12 - $this->labelWidth) . "\">\n";
			$html .= "\t<div class=\"checkbox\">\n";
			$html .= "\t\t<label>\n";
			$html .= "\t\t\t<input type=\"checkbox\"{$this->classAttr()}{$this->idAttr()}{$this->getAttributes()}>\n";
			$html .= "\t\t{$this->label}</label>\n";
			$html .= "\t</div>\n\t</div>\n";
		}
		$html .= "</div>\n";

		return $html;
	}
}