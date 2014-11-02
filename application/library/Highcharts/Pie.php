<?php namespace Highcharts;

use Highcharts\Pie\Serie;

class Pie extends AbstractChart {
	
	/**
	 * Set the title
	 * @param string $title
	 * @return \Highcharts\Line
	 */
	public function title($title) {
		return $this->set('title', array(
			'text' => $title
		));
	}
	
	/**
	 * Add the serie to chart
	 * @param \Hightcharts\Pie\Serie;
	 * @return \Highcharts\Line
	 */
	public function addSerie(Serie $serie) {
		$this->series[] = $serie->getOptions();
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Highcharts\AbstractChart::getOptions()
	 */
	protected function getOptions() {
		$this->set('tooltip', array('pointFormat' => '{series.name}: <b>{point.percentage:.1f}%</b>'));
		
		$this->set('plotOptions', array(
			'pie' => array(
				'allowPointSelect' => true,
				'cursor' => 'pointer',
				'dataLabels' => array(
					'enabled' => false,
					'color' => '#000000',
					'connectorColor' => '#000000',
					'format' => '<b>{point.name}</b>: {point.percentage:.1f} %'
				),
				'showInLegend' => true
			)
		));
		
		$this->set('series', $this->series);
		
		return parent::getOptions();
	}
	
}