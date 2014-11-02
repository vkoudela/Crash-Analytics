<?php namespace Chart;

use Highcharts\Pie\Serie;

class ProblematicBrands extends \Highcharts\Pie {
	
	public function __construct($minutes) {
		$data = \Crash\Submit::getProblematicBrandModels($minutes * -1);

		$serie = Serie::create('brand');
		$others = 0; $cnt = 0;
		foreach ($data as $brand => $count) {
			if ($cnt++ >= 6) {
				$others += $count;
			} else {
				$serie->addData($brand, $count);
			}
		}
		
		if ($others > 0) {
			$serie->addData('other', $others);
		}
		
		$this->title('Total brands: ' . sizeof($data));
		$this->addSerie($serie);
	}
	
}