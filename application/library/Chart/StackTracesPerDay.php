<?php namespace Chart;

use Koldy\Db\Select;
use Koldy\Cache;

class StackTracesPerDay extends \Highcharts\Line {
	
	public function __construct($stackTraceId, $days) {
		$this->title("Last {$days} days analysis")
			->titleOnYAxis('count per day');

		$offset = \Misc::getUserTimezoneOffsetInMinutes();
		$cacheKey = "stacks-{$stackTraceId}-per-last-{$days}-days-offset-{$offset}min";

		$from = new \DateTime(\Misc::userDate('Y-m-d 00:00:00'));
		$from->modify("-{$days} day");

		$records = Cache::getOrSet($cacheKey, function() use ($from, $stackTraceId, $days, $offset) {
			$operator = ($offset < 0) ? '-' : '+';
			$offsetAbs = abs($offset);
			
			$today = new \DateTime(\Misc::userDate('Y-m-d 00:00:00'));
			$query = \Crash\Archive::query()
				->field("DATE(created_at + INTERVAL {$offset} MINUTE)", 'time')
				->field('COUNT(*)', 'total')
				->where('stack_trace_id', $stackTraceId)
				->where('created_at', '>=', \Db::expr("'{$from->format('Y-m-d H:i:s')}' {$operator} INTERVAL {$offsetAbs} MINUTE"))
// 				->where('created_at', '<', $today->format('Y-m-d 23:59:59'))
				->groupBy(1)
				->orderBy(1);
			
			return $query->fetchAllObj();
		}, (3720 - date('i')*60 + date('s')));
		
		$data = array();
		
		foreach ($records as $r) {
			$data[$r->time] = $r->total;
		}
		
		$serieData = array();
		$startMinute = (int) $from->format('i');
		
		for ($day = $from, $today = \Misc::userDate('Y-m-d'); $day->format('Y-m-d') <= $today; $day->modify('+1 day')) {
			$date = $day->format('Y-m-d');
			$serieData[$date] = isset($data[$date]) ? (int) $data[$date] : 0;
			$this->addOnXAxis($day->format('jS'));
		}
		
		$this->addSerie('requests', array_values($serieData));
	}
	
}