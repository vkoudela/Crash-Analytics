<?php namespace Highcharts;

class Line extends AbstractChart {
	
	protected $xLabels = array();
	
	protected $yAxis = array();
	
	protected $series = array();
	
	/**
	 * Set the title
	 * @param string $title
	 * @return \Highcharts\Line
	 */
	public function title($title) {
		return $this->set('title', array(
			'text' => $title,
			'x' => -20
		));
	}
	
	/**
	 * Add label on X axis
	 * @param string|array $label
	 * @return \Highcharts\Line
	 */
	public function addOnXAxis($label) {
		if (is_array($label)) {
			$this->xLabels = $label;
		} else {
			$this->xLabels[] = $label;
		}
		return $this;
	}
	
	/**
	 * Set the title on Y axis
	 * @param string $title
	 * @return \Highcharts\Line
	 */
	public function titleOnYAxis($title) {
		$this->yAxis['title'] = array('text' => $title);
		return $this;
	}
	
	/**
	 * Set the legend
	 * @param string $verticalAlign
	 * @return \Highcharts\Line
	 */
	public function legend($verticalAlign = 'middle') {
		return $this->set('legend', array(
			'layout' => 'vertical',
			'align' => 'right',
			'verticalAlign' => $verticalAlign,
			'borderWidth' => 0
		));
	}
	
	/**
	 * Make selection shared with tooltip
	 * @return \Highcharts\Line
	 */
	public function tooltipShared() {
		if ($this->has('tooltip')) {
			$tooltip = $this->get('tooltip');
		} else {
			$tooltip = array();
		}
		
		$tooltip['crosshairs'] = true;
		$tooltip['shared'] = true;
		
		return $this->set('tooltip', $tooltip);
	}
	
	/**
	 * Add the serie to chart
	 * @param string $name
	 * @param array $data
	 * @return \Highcharts\Line
	 */
	public function addSerie($name, array $data) {
		$this->series[] = array(
			'name' => $name,
			'data' => $data
		);
		
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Highcharts\AbstractChart::getOptions()
	 */
	protected function getOptions() {
		$this->set('chart', array('type' => 'spline'));
		$this->set('xAxis', array(
			'categories' => $this->xLabels
		));
		
		$this->set('yAxis', $this->yAxis);
		$this->set('series', $this->series);
		
		return parent::getOptions();
	}
	
}