<?php namespace Bootstrap\Response\Input;

use Koldy\Input;
use Koldy\Validator;
use Koldy\Html;

class Image extends \Bootstrap\Response\Form {
	
	public function __construct() {
		parent::__construct();
		$this->remove('icon');
		
		$this->set('__field_name', Input::post('__field_name'));
		$this->set('__field_id', Input::post('__field_id'));
		$this->set('__form_id', Input::post('__form_id'));
	}
	
	/**
	 * Show the image in response
	 * @param string $url
	 * @param array $formValues additional hidden values that will be submitted with form
	 * @return \Bootstrap\Response\Input\Image
	 */
	public function html($html) {
		$this->set('html', $html);
		return $this;
	}
	
	/**
	 * Append HTML
	 * @param string $url
	 * @param array $formValues additional hidden values that will be submitted with form
	 * @return \Bootstrap\Response\Input\Image
	 */
	public function appendHtml($html) {
		$this->set('append', $html);
		return $this;
	}
	
	public function flush() {
		if (sizeof($this->fieldValues) > 0) {
			$this->set('fieldValues', $this->fieldValues);
		}
		
		if (sizeof($this->fieldErrors) > 0) {
			$this->set('fieldErrors', $this->fieldErrors);
		}
		
		header('Connection: close');
		ob_start();
	
		$json = self::encode($this->getResponse());
		echo "<script type=\"text/javascript\">parent.Xcms.Input.Image.serverResponse({$json});</script>";
		
		$size = ob_get_length();
		header("Content-Length: {$size}");
	
		ob_end_flush();
		flush();
	
		if ($this->workAfterResponse !== null) {
			$fn = $this->workAfterResponse;
			$fn();
		}
	}
	
}