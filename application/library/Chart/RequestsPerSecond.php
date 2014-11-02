<?php namespace Chart;

use Koldy\Db\Query;

class RequestsPerSecond extends \Highcharts\Line {
	
	public function __construct($minutes) {
		$this->titleOnYAxis('number of requests');
		
		$data = \Crash\Submit::getRequestsPerMinute($minutes * -1);
		$serieData = array();
		$from = new \DateTime(gmdate('Y-m-d H:i:00'));
		$from->modify("-{$minutes} minute");
		$startMinute = (int) $from->format('i');
		
		$total = 0;
		for ($counter = 0, $minute = $startMinute; $counter < $minutes; $counter++, $minute++) {
			if ($minute == 60) {
				$minute = 0;
			}
			
			$this->addOnXAxis($minute);
			$serieData[$minute] = (isset($data[$minute])) ? $data[$minute] : 0;
			$total += $serieData[$minute];
		}
		
		$this->title('Total requests: ' . $total);
		$this->addSerie('requests', array_values($serieData));
	}
	
}