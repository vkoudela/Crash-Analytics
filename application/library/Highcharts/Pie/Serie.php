<?php namespace Highcharts\Pie;

class Serie {
	
	protected $options = array();
	
	public function __construct($name) {
		$this->options['type'] = 'pie';
		$this->options['name'] = $name;
		$this->options['data'] = array();
	}
	
	/**
	 * Add the data to this serie
	 * @param string $name
	 * @param int|float $value
	 * @return \Highcharts\Pie\Serie
	 */
	public function addData($name, $value) {
		$this->options['data'][] = array($name, $value);
		return $this;
	}
	
	/**
	 * Get the options
	 * @return multitype:
	 */
	public function getOptions() {
		return $this->options;
	}
	
	/**
	 * Create new instance
	 * @param string $name
	 * @return \Highcharts\Pie\Serie
	 */
	public static function create($name) {
		return new static($name);
	}
}