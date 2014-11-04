<?php

use Koldy\Session;
use Koldy\View;
use Koldy\Timezone;

abstract class AbstractSessionController {
	
	/**
	 * @var array
	 */
	protected $user = null;
	
	public function before() {
		if (!Session::has('user')) {
			return View::create('login')
				->with('loginForm', IndexController::getLoginForm());
		} else {
			$this->user = Session::get('user');
		}
	}
	
	/**
	 * Get data from user data in session
	 * @param string $what
	 * @return mixed|NULL
	 */
	protected function getUser($what = null) {
		if ($what === null) {
			return $this->user;
		} else if (isset($this->user[$what])) {
			return $this->user[$what];
		} else {
			return null;
		}
	}
	
	/**
	 * Get the timezone calculated date
	 * @param string $format
	 * @param int $timestamp
	 * @return string
	 */
	protected function date($format, $timestamp = null) {
		return Timezone::date($this->getUser('timezone'), $format, $timestamp);
	}
	
	protected function duration($timeStart, $timeEnd) {
		$timeStart = strtotime($timeStart);
		$timeEnd = strtotime($timeEnd);
		
		$difference = abs($timeEnd - $timeStart);
		
		$hours = floor($difference / 3600);
		$difference -= ($hours * 3600);
		
		$minutes = floor($difference / 60);
		$difference -= ($minutes * 60);
		
		$seconds = $difference;
		
		if ($hours < 10) {
			$hours = "0{$hours}";
		}
		
		if ($minutes < 10) {
			$minutes = "0{$minutes}";
		}
		
		if ($seconds < 10) {
			$seconds = "0{$seconds}";
		}
		
		return "{$hours}:{$minutes}:{$seconds}";
	}
}
