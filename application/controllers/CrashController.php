<?php

class CrashController {
	
	public function addAction() {
		return Json::create(array(
			'success' => true
		))->after(function() {
			\Crash\Submit::processRequest('Android');
		});
	}
	
	public function addAjax() {
		return $this->addAction();
	}
}