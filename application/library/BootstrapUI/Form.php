<?php namespace BootstrapUI;

use Bootstrap\Input\AbstractElement;
use Bootstrap\NestableElement;
use BootstrapUI\Input\FileUpload;
use Koldy\Convert;

class Form extends \Bootstrap\Form {
	
	/**
	 * Create the form
	 * @param string $action
	 */
	public function __construct($action = null) {
		parent::__construct($action);
		$this->addClass('x-form');
	}
	
	/**
	 * Get the fields from form
	 * @return array
	 */
	public function getFields() {
		$values = array();
		foreach ($this->elements as $key => $element) {
			if ($element instanceof AbstractElement || $element instanceof FileUpload) {
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
			if ($element instanceof AbstractElement || $element instanceof FileUpload) {
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
	 * (non-PHPdoc)
	 * @see \Bootstrap\Form::getHtml()
	 */
	public function getHtml() {
		$this->data('file-count-limit', (int) ini_get('max_file_uploads'));
		
		$postMaxSize = Convert::stringToBytes(ini_get('post_max_size'));
		$uploadMaxSize = Convert::stringToBytes(ini_get('upload_max_filesize'));
		
		$this->data('max-size', $postMaxSize >= $uploadMaxSize ? $postMaxSize : $uploadMaxSize);
		
		return parent::getHtml();
	}

}