<?php

use Koldy\Url;
use Koldy\Session;

class Menu {
	
	/**
	 * Get reports links
	 * @return multitype:string
	 */
	public static function getReportLinks() {
		$user = Session::get('user');
		
		$newStackTraces = isset($user['stats']) ? $user['stats']['new_stack_traces'] : 0;
		
		if ($newStackTraces > 0) {
			$newStacks = ' ' . Bootstrap::label($newStackTraces)->color('red');
		} else {
			$newStacks = '';
		}
		
		return array(
			Url::href('stack-traces') => 'Stack Traces' . $newStacks,
			Url::href('brands') => 'Brand reports',
			Url::href('packages') => 'Package reports',
			Url::href('os-versions') => 'OS version reports',
			Url::href('countries') => 'Country reports',
			Url::href('providers') => 'Provider reports'
		);
	}
	
}