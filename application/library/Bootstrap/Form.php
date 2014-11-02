<?php namespace Bootstrap;

class Form extends NestableElement {

	/**
	 * The action where form will be submitted
	 * @var string
	 */
	protected $action = null;
	
	/**
	 * Form method
	 * @var string
	 */
	protected $method = 'POST';

	/**
	 * Form buttons
	 * @var array
	 */
	protected $buttons = array();

	/**
	 * Help text of form
	 * @var string
	 */
	protected $helpText = null;

	/**
	 * Position of buttons in form
	 * @var string
	 */
	private $buttonsPosition = 'bottom';

	/**
	 * The vertical version mustn't have this property set. If you set this,
	 * then horizontal version will be generated with the label width set here.
	 * @param  integer $labelWidth The number between 1 and 11. The optimal is 2.
	 */
	private $labelWidth = null;
	
	/**
	 * Create the form
	 * @param string $action
	 */
	public function __construct($action = null) {
		$this->action = ($action === null ? $_SERVER['REQUEST_URI'] : $action);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\NestableElement::add()
	 */
	public function add($element) {
		if ($element instanceof Input\AbstractElement) {
			$this->elements[$element->getName()] = $element;
			
		} else if (is_object($element)) {
			$this->elements[$element->getId()] = $element;
			
		} else {
			$this->elements[HtmlElement::getGlobalId()] = $element;
			
		}
		return $this;
	}

	/**
	 * Add button to the form
	 * @param mixed $button
	 * @return \Bootstrap\Form
	 */
	public function addButton($button) {
		$this->buttons[] = $button;
		return $this;
	}

	/**
	 * Set the form's method
	 * @param string $method
	 * @return \Bootstrap\Form
	 */
	public function method($method) {
		$this->method = $method;
		return $this;
	}

	/**
	 * Make the form horizontal
	 * @param number $labelWidth
	 * @return \Bootstrap\Form
	 */
	public function horizontal($labelWidth = 2) {
		$this->labelWidth = $labelWidth;
		return $this->addClass('form-horizontal');
	}

	/**
	 * Set the help text in form
	 * @param string $helpText
	 * @return \Bootstrap\Form
	 */
	public function helpText($helpText) {
		$this->helpText = $helpText;
		return $this;
	}

	/**
	 * Set the buttons position
	 * @param string $position
	 * @return \Bootstrap\Form
	 * @throws Exception
	 */
	public function buttonsOn($position) {
		switch(strtolower($position)) {
			case 'top': $this->buttonsPosition = 'top'; break;
			case 'bottom': $this->buttonsPosition = 'bottom'; break;
			default: throw new Exception('Can not set buttons position in form to ' . $position); break;
		}
		return $this;
	}

	/**
	 * Set the values of input elements in form
	 * @param array $values
	 * @return \Bootstrap\Form
	 */
	public function setValues(array $values) {
		foreach ($values as $field => $value) {
			if (isset($this->elements[$field])) {
				$f = $this->elements[$field];
				if ($f instanceof Input\AbstractElement) {
					if ($f instanceof Input\Checkbox) {
						$f->checked($value);
					} else {
						$f->value($value);
					}
				}
			} else {
				foreach ($this->elements as $element) {
					if ($element instanceof NestableElement) {
						$this->findFieldAndSetValue($element, $field, $value);
					}
				}
			}
		}
		return $this;
	}
	
	/**
	 * Find all nested fields and set the value
	 * @param NestableElement $element
	 * @param string $field
	 * @param mixed $value
	 */
	private function findFieldAndSetValue(NestableElement $element, $field, $value) {
		foreach ($element->getElements() as $e) {
			if ($e instanceof Input\AbstractElement && $e->getName() == $field) {
				if ($e instanceof Input\Checkbox) {
					$e->checked($value);
				} else {
					$e->value($value);
				}
			} else if ($e instanceof NestableElement) {
				$this->findFieldAndSetValue($e, $field, $value);
			}
		}
	}

	/**
	 * Get the values from form
	 * @return array
	 */
	public function getValues() {
		$values = array();
		foreach ($this->elements as $key => $element) {
			if (method_exists($element, 'getValue')) {
				$values[$key] = $element->getValue();
			} else if ($element instanceof NestableElement) {
				$val = $this->getValuesFromNestable($element);
				if (sizeof($val) > 0) {
					$values = array_merge($values, $val);
				}
			}
		}
		return $values;
	}
	
	/**
	 * Get values from netstable element
	 * @param NestableElement $element
	 * @return array
	 */
	private function getValuesFromNestable(NestableElement $element) {
		$elements = $element->getElements();
		$values = array();
		
		foreach ($elements as $element) {
			if (method_exists($element, 'getValue')) {
				$values[$key] = $element->getValue();
			} else if ($element instanceof NestableElement) {
				$val = $this->getValuesFromNestable($element);
				if (is_array($val)) {
					$values = array_merge($values, $val);
				} else {
					$values[$key] = $val;
				}
			}
		}
		
		return $values;
	}

	/**
	 * FIXME: This is not working
	 * Get the object instance of field name
	 * @param string $name
	 * @return mixed or null if field is not found
	 */
	public function getField($name) {
		return isset($this->fields[$name])
			? $this->fields[$name]
			: null;
	}
	
	/**
	 * Get the fields from form
	 * @return array
	 */
	public function getFields() {
		$values = array();
		foreach ($this->elements as $key => $element) {
			if ($element instanceof Input\AbstractElement) {
				$values[$key] = $element;
			} else if ($element instanceof NestableElement) {
				$val = $this->getFieldsFromNestable($element);
				if (sizeof($val) > 0) {
					$values = array_merge($values, $val);
				}
			}
		}
		return $values;
	}
	
	/**
	 * Get fields from netstable element
	 * @param NestableElement $element
	 * @return array
	 */
	private function getFieldsFromNestable(NestableElement $element) {
		$elements = $element->getElements();
		$values = array();
		
		foreach ($elements as $element) {
			if ($element instanceof Input\AbstractElement) {
				$values[$key] = $element;
			} else if ($element instanceof NestableElement) {
				$val = $this->getFieldsFromNestable($element);
				if (sizeof($val) > 0) {
					$values = array_merge($values, $val);
				}
			}
		}
		
		return $values;
	}

	/**
	 * Add submit button
	 * @param string $buttonText
	 * @param string $name
	 * @return Bootstra\Form
	 */
	public function addSubmit($buttonText, $name = null) {
		$button = \Bootstrap::button($buttonText)
			->type('submit')
			->color('primary');
		
		if ($name !== null) {
			$button->name($name);
		}
		
		return $this->addButton($button);
	}

	/**
	 * Get HTML for buttons
	 * @param string $position
	 * @return string
	 */
	private function renderButtons($position) {
		$html = '';
		if ($this->buttonsPosition == $position && sizeof($this->buttons) > 0) {
			$html .= "\n<div class=\"form-group\">\n";

			if ($this->labelWidth !== null) {
				$html .= "\t<div class=\"col-lg-offset-{$this->labelWidth} col-lg-" . (12 - $this->labelWidth) . "\">\n";
			}

			foreach ($this->buttons as $element) {
				$html .= "\t" . $element . "\n";
			}

			$html .= "\t<span class=\"x-status-icon\"></span>\n";

			if ($this->labelWidth !== null) {
				$html .= "</div>\n";
			}

			$html .= "</div>\n";
		}
		return $html;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$html = "<form action=\"{$this->action}\" method=\"{$this->method}\"{$this->idAttr()} role=\"form\"{$this->classAttr()}{$this->getAttributes()}>\n";

		$html .= $this->renderButtons('top');

		foreach ($this->elements as $element) {
			if ($element instanceof Input\Hidden) {
				$html .= $element;
			} else if ($this->labelWidth !== null && is_object($element) && method_exists($element, 'getLabelWidth') && method_exists($element, 'labelWidth') && $element->getLabelWidth() === null) {
				$element->labelWidth($this->labelWidth);
				$html .= $element;
			} else {
				$html .= $element;
			}
		}

		$html .= $this->renderButtons('bottom');

		if ($this->labelWidth === null) {
			$html .= "\t<span class=\"help-block\">{$this->helpText}</span>\n";
		} else {
			$html .= "<div class=\"form-group\">\n";
			$html .= "\t<div class=\"col-sm-{$this->labelWidth}\"></div>\n";
			$html .= "\t<div class=\"col-sm-" . (12 - $this->labelWidth) . " x-help-block\">\n";
			$html .= "\t\t<span class=\"help-block\">{$this->helpText}</span>\n";
			$html .= "\t</div>\n";
			$html .= "</div>\n";
		}
		$html .= '</form>';
		return $html;
	}

}