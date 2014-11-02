<?php namespace Bootstrap\Input;

class Textarea extends AbstractElement {

	/**
	 * The value
	 * @var string
	 */
	protected $value = null;

	/**
	 * Field label
	 * @var string
	 */
	protected $label = null;

	/**
	 * Label width
	 * @var int
	 */
	protected $labelWidth = null;

	/**
	 * Construct the object
	 * @param string $name
	 * @param string $value
	 * @param string $label
	 */
	public function __construct($name, $value = null, $label = null) {
		$this->name = $name;
		$this->value = $value;
		$this->label = $label;
	}

	/**
	 * Set the element label
	 * @param string $label
	 * @return \Bootstrap\Input\Textarea
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
	 * @return  \Bootstrap\Input\Text
	 */
	public function labelWidth($width) {
		$this->labelWidth = $width;
		return $this;
	}

	/**
	 * Get the label width
	 * @return number
	 */
	public function getLabelWidth() {
		return $this->labelWidth;
	}

	/**
	 * Set the value
	 * @param string $value
	 * @return \Bootstrap\Input\Textarea
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
	 * Set the placeholder
	 * @param string $placeholder
	 * @return \Bootstrap\Input\Textarea
	 */
	public function placeholder($placeholder) {
		return $this->setAttribute('placeholder', $placeholder);
	}
	
	/**
	 * Set how many rows will textarea have
	 * @param int $rows
	 * @return \Bootstrap\Input\Textarea
	 */
	public function rows($rows) {
		return $this->setAttribute('rows', $rows);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$html = '<div class="form-group">';
		
		if ($this->value !== null) {
			$this->value = stripslashes($this->value);
		}

		if ($this->label !== null) {
			if ($this->labelWidth === null) {
				$html .= "\n\t<label for=\"{$this->getId()}\" class=\"control-label\">{$this->label}</label>\n";
			} else {
				$html .= "\n\t<label for=\"{$this->getId()}\" class=\"col-lg-{$this->labelWidth} control-label\">{$this->label}</label>\n";
			}
		}

		if ($this->name !== null) {
			$this->setAttribute('name', $this->name);
		}

		if ($this->labelWidth === null) {
			$html .= "\t<textarea{$this->classAttr('form-control')}{$this->idAttr()}{$this->getAttributes()}>{$this->value}</textarea>\n";
		} else {
			$html .= "\t<div class=\"col-lg-" . (12 - $this->labelWidth) . "\">\n";
			$html .= "\t\t<textarea{$this->classAttr('form-control')}{$this->idAttr()}{$this->getAttributes()}>{$this->value}</textarea>\n";
			$html .= "\t</div>\n";
		}
		$html .= "</div>\n";

		return $html;
	}
}