<?php namespace BootstrapUI\Input;

class File extends FileUpload {
	
	/**
	 * This file element can upload multiple files
	 * @return \BootstrapUI\Input\File
	 */
	public function multiple() {
		if (strpos($this->name, '[]') === false) {
			$this->name .= '[]';
		}
		return $this->setAttribute('multiple', 'multiple');
	}
	
}