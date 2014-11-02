<?php namespace Chart;

use Highcharts\Pie\Serie;

class ProblematicApps extends \Highcharts\Pie {
	
	public function __construct($minutes) {
		$data = \Crash\Submit::getProblematicApps($minutes * -1);
		$serie = Serie::create('app');
		
		$total = 0; $cnt = 0; $other = 0;
		foreach ($data as $app => $count) {
			if ($cnt++ < 6) {
				$serie->addData($app, $count);
				$total += $count;
			} else {
				$other += $count;
				$total += $count;
			}
		}
		
		if ($other > 0) {
			$serie->addData('other', $other);
		}
		
		$this->title('Total apps: ' . sizeof($data));
		
		$this->addSerie($serie);
	}
	
}