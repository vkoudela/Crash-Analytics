<?php namespace BootstrapUI\Input;

use Koldy\Convert;

abstract class FileUpload extends \Bootstrap\HtmlElement {
	
	/**
	 * The element name in POST request
	 * @var string
	 */
	protected $name = null;
	
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
	 * The array of allowed MIME types to be uploaded
	 * @var array
	 */
	protected $allowedMimes = array();
	
	/**
	 * The file list position
	 * @var string
	 * @example 'above' or 'below' - if null, then it will be nowhere
	 */
	protected $fileListPosition = null;
	
	/**
	 * Array of files as existing files in this list
	 * @var array of \BootstrapUI\Input\FilePreview
	 */
	protected $files = array();
	
	/**
	 * Construct the element
	 * @param string $name
	 * @param string $value
	 * @param string $label
	 */
	public function __construct($name, $label = null) {
		$this->name = $name;
		$this->label = $label;
		$this->prepend(\Bootstrap::icon('file'));
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
	 * Get the element's value
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
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
	 * The file list position related to input field
	 * @param string $position
	 * @return \BootstrapUI\Input\FileUpload
	 */
	public function fileListPosition($position) {
		$this->fileListPosition = $position;
		return $this;
	}
	
	/**
	 * Add new file preview
	 * @param FilePreview $filePreview
	 * @return \BootstrapUI\Input\FileUpload
	 */
	public function filePreview(FilePreview $filePreview) {
		$this->files[] = $filePreview;
		return $this;
	}
	
	/**
	 * Set mime type that is allowed for upload
	 * @param string $mimeType
	 * @return \BootstrapUI\Input\FileUpload
	 */
	public function accept($mimeType) {
		if (is_array($mimeType)) {
			$this->allowedMimes = strtolower($mimeType);
		} else {
			$this->allowedMimes[] = strtolower($mimeType);
		}
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
	
			$html .= "\t\t<input type=\"file\"{$this->classAttr('form-control')}{$this->idAttr()}{$this->getAttributes()}>\n";
	
			if ($this->append !== null) {
				$html .= "\t\t<span class=\"input-group-addon\">{$this->append}</span>\n";
			}
	
			if ($this->prepend !== null || $this->append !== null) {
				$html .= "\t</div>\n";
			}
		} else {
			$html .= "\t<div class=\"col-sm-" . (12 - $this->labelWidth) . "\">\n";
			$html .= "\t\t<input type=\"file\"{$this->classAttr('form-control')}{$this->idAttr()}{$this->getAttributes()}>\n";
			$html .= "\t</div>\n";
		}
		return $html;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$this->setAttribute('accept', implode(',', $this->allowedMimes));
		
		$html = '<div class="form-group">';
	
		if ($this->label !== null && $this->labelWidth === null) {
			$html .= "\n\t<label for=\"{$this->getId()}\" class=\"control-label\">{$this->label}</label>\n";
		} else if ($this->label !== null || $this->labelWidth !== null) {
			$html .= "\n\t<label for=\"{$this->getId()}\" class=\"col-sm-{$this->labelWidth} control-label\">{$this->label}</label>\n";
		}
	
		if ($this->name !== null) {
			$this->setAttribute('name', $this->name);
		}
	
		$html .= $this->getInputHtml();
	
		$html .= "</div>\n";
	
		return $html;
	}
	
}