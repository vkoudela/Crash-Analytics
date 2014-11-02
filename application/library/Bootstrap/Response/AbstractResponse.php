<?php namespace Bootstrap\Response;

abstract class AbstractResponse extends \Koldy\Json {
	
	public function __construct() {
		$this->set('success', true);
	}

}