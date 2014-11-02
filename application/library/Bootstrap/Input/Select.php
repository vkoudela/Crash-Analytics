<?php namespace Bootstrap\Input;

use Bootstrap\Exception;

class Select extends AbstractElement {

	/**
	 * Selected value
	 * @var string
	 */
	protected $value = null;
	
	/**
	 * Array of available values
	 * @var array
	 */
	protected $values = array();
	
	/**
	 * The key field index if values are array of more then two values
	 * @var string
	 */
	private $keyField = null;
	
	/**
	 * The value field index if values are array of more then two values
	 * @var string
	 */
	private $valueField = null;

	/**
	 * Field label
	 * @var string
	 */
	protected $label = null;

	/**
	 * The label width
	 * @var int
	 */
	protected $labelWidth = null;

	/**
	 * Construct the object for select element/dropdown/combobox - call it whatever
	 * @param string $name
	 * @param string $label [optional]
	 * @param array $values [optional] assoc array
	 * @param mixed $value [optional]
	 */
	public function __construct($name, $label = null, array $values = array(), $value = null) {
		$this->name = $name;
		$this->value = $value;
		$this->values = $values;
		$this->label = $label;
	}

	/**
	 * Set the field label
	 * @param string $label
	 * @return \Bootstrap\Input\Select
	 */
	public function label($label) {
		$this->label = $label;
		return $this;
	}

	/**
	 * Get the field label
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * The bootstrap width, between 1 and 11
	 * @param  integer $width
	 * @return  \Bootstrap\Input\Select
	 */
	public function labelWidth($width) {
		if ($width < 1 || $width > 11) {
			throw new Exception('Can not render select element with label not in range of 1-11');
		}
		$this->labelWidth = $width;
		return $this;
	}

	/**
	 * Get label width
	 * @return number
	 */
	public function getLabelWidth() {
		return $this->labelWidth;
	}

	/**
	 * Set the element's value
	 * @param string $value
	 * @return \Bootstrap\Input\Select
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
	 * Set the array of values. If you don't pass assoc array, then define which
	 * element is key and which one is value.
	 * @param array $values
	 * @param string $keyField
	 * @param string $valueField
	 * @return \Bootstrap\Input\Select
	 */
	public function values(array $values, $keyField = null, $valueField = null) {
		$this->values = $values;
		$this->keyField = $keyField;
		$this->valueField = $valueField;
		
		return $this;
	}
	
	/**
	 * Make it multiple choice
	 * @return \Bootstrap\Input\Select
	 */
	public function multiple() {
		if (strpos($this->name, '[') === false && strpos($this->name, ']') === false) {
			$this->name .= '[]';
		}
		return $this->setAttribute('multiple', 'multiple');
	}
	
	/**
	 * Get the values
	 * @return array
	 */
	public function getValues() {
		return $this->values;
	}
	
	/**
	 * Get the options for rendering
	 * @return string
	 */
	private function getOptions() {
		$s = '';
		if ($this->keyField !== null && $this->valueField !== null) {
			$keyField = $this->keyField;
			$valueField = $this->valueField;
			
			foreach ($this->values as $option) {
				if (is_object($option)) {
					$key = $option->$keyField;
					$value = $option->$valueField;
				} else {
					$key = $option[$keyField];
					$value = $option[$valueField];
				}
				
				$selected = false;
				if ($this->value !== null) {
					if (is_array($this->value)) {
						$selected = in_array($key, $this->value);
					} else {
						$selected = ($this->value == $key);
					}
				}
				
				$selectedHtml = $selected ? ' selected="selected"' : '';
				$s .= "\t\t<option value=\"{$key}\"{$selectedHtml}>{$value}</option>\n";
			} 
		} else {
			foreach ($this->values as $key => $value) {
				$selected = false;
				if ($this->value !== null) {
					if (is_array($this->value)) {
						$selected = in_array($key, $this->value);
					} else {
						$selected = ($this->value == $key);
					}
				}
				
				$selectedHtml = $selected ? ' selected="selected"' : '';
				$s .= "\t\t<option value=\"{$key}\"{$selectedHtml}>{$value}</option>\n";
			}
		}
		
		return $s;
	}
	
	/**
	 * Get the input html
	 * @return string
	 */
	protected function getInputHtml() {
		$html  = "\t\t<select{$this->classAttr('form-control')}{$this->idAttr()}{$this->getAttributes()}>\n";
		$html .= $this->getOptions();
		$html .= "\t\t</select>\n";
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

		if ($this->labelWidth === null) {
			$html .= $this->getInputHtml();
		} else {
			$html .= "\t<div class=\"col-sm-" . (12 - $this->labelWidth) . "\">\n";
				$html .= $this->getInputHtml();
			$html .= "\t</div>\n";
		}
		$html .= "</div>\n";

		return $html;
	}
}
