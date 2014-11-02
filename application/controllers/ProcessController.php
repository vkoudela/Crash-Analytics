<?php

use Koldy\Log;
use Koldy\Application\CliController;
use Koldy\Db\Select;
use Koldy\Db;
use Stack\Trace as StackTrace;

class ProcessController extends CliController {
	
	private $stackTraceIds = array();
	
	public function __construct() {
		parent::__construct();
		ini_set('memory_limit', '512M');
	}
	
	public function precalculateAction() {
		$x = new Calculation();
		$x->calculate();
	}
	
	public function stackTracesAction() {
		Log::info('Started rebuilding stack traces');
		Stack\Trace::rebuild();
	}
}