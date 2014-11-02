<?php

class Highcharts {
	
	/**
	 * New Line graph object
	 * @return \Highcharts\Line
	 */
	public static function line() {
		return new Highcharts\Line();
	}
	
	/**
	 * New Pie chart
	 * @return \Highcharts\Pie
	 */
	public static function pie() {
		return new Highcharts\Pie();
	}
	
}