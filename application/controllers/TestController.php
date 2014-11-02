<?php

use Koldy\Cache;
use Koldy\Log;
class TestController {
	
	
	public function regAction() {
		var_dump(Provider::normalizeHostName('80.misp.ru'));
	}

	public function cacheAction() {
		var_dump(Cache::set('test', 'testis val', 15));
	}
	
	public function logAction() {
		Log::debug('AE1');
		Log::notice('AE2');
		Log::notice('AE3');
		echo "OK";
	}
	
	public function logErrorAction() {
		Log::error('TEST ERROR');
	}
}