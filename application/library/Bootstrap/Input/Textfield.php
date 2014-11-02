<?php namespace Bootstrap\Input;

class Textfield extends AbstractElement {

	/**
	 * The element's value
	 * @var mixed
	 */
	protected $value = null;

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
	 * The element's type, by default 'text'
	 * @var string
	 */
	protected $type = 'text';
	
	/**
	 * Prepend with anything
	 * @var string
	 */
	protected $prepend = null;
	
	/**
	 * Append with anything
	 * @var string
	 */
	protected $append = null;

	/**
	 * Construct the element
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
	 * Set the label
	 * @param string $label
	 * @return \Bootstrap\Input\Text
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
	 * @return string
	 */
	public function getLabelWidth() {
		return $this->labelWidth;
	}

	/**
	 * Set the element's value
	 * @param mixed $value
	 * @return \Bootstrap\Input\Text
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
	 * Set the field's type
	 * @param string $type text, password, datetime, datetime-local, date, month, time, week, number, email, url, search, tel or color
	 * @return \Bootstrap\Input\Text
	 */
	public function type($type) {
		$this->type = $type;
		return $this;
	}

	/**
	 * Set the element's placeholder
	 * @param string $placeholder
	 * @return \Bootstrap\Input\Text
	 */
	public function placeholder($placeholder) {
		return $this->setAttribute('placeholder', $placeholder);
	}
	
	/**
	 * Make this field required
	 * @return \Bootstrap\Input\Textfield
	 */
	public function required() {
		return $this->setAttribute('required', 'required');
	}
	
	/**
	 * Prepend with element
	 * @param mixed $element
	 * @return \Bootstrap\Input\Text
	 */
	public function prepend($element) {
		$this->prepend = $element;
		return $this;
	}
	
	/**
	 * Append with element
	 * @param mixed $element
	 * @return \Bootstrap\Input\Text
	 */
	public function append($element) {
		$this->append = $element;
		return $this;
	}
	
	/**
	 * Get the HTML for input
	 * @return string
	 */
	protected function getInputHtml() {
		$html = '';
		if ($this->labelWidth === null) {
			if ($this->prepend !== null || $this->append !== null) {
				$html .= "\t<div class=\"input-group\">\n";
			}
				
			if ($this->prepend !== null) {
				$html .= "\t\t<span class=\"input-group-addon\">{$this->prepend}</span>\n";
			}
				
			$html .= "\t\t<input type=\"{$this->type}\"{$this->classAttr('form-control')}{$this->idAttr()}{$this->getAttributes()}>\n";
				
			if ($this->append !== null) {
				$html .= "\t\t<span class=\"input-group-addon\">{$this->append}</span>\n";
			}
				
			if ($this->prepend !== null || $this->append !== null) {
				$html .= "\t</div>\n";
			}
		} else {
			$html .= "\t<div class=\"col-sm-" . (12 - $this->labelWidth) . "\">\n";
			$html .= "\t\t<input type=\"{$this->type}\"{$this->classAttr('form-control')}{$this->idAttr()}{$this->getAttributes()}>\n";
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
			$html .= "\n\t<label for=\"{$this->getId()}\" class=\"control-label\">{$this->label}</label>\n";
		} else if ($this->label !== null || $this->labelWidth !== null) {
			$html .= "\n\t<label for=\"{$this->getId()}\" class=\"col-sm-{$this->labelWidth} control-label\">{$this->label}</label>\n";
		}

		if ($this->name !== null) {
			$this->setAttribute('name', $this->name);
		}

		if ($this->value !== null) {
			$this->setAttribute('value', str_replace("\"", '&quot;', stripslashes($this->value)));
		}

		$html .= $this->getInputHtml();
		
		$html .= "</div>\n";

		return $html;
	}
}