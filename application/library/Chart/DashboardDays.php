<?php namespace Chart;

use Koldy\Log;
use Koldy\Session;
use Koldy\Db\Select;
use Koldy\Cache;

class DashboardDays extends \Highcharts\Line {
	
	public function __construct($days) {
		$user = Session::get('user');
		
		$this->title("Crash reports per days (UTC time)")
			->titleOnYAxis('number of requests')
			->tooltipShared();

// 		$offset = \Misc::getUserTimezoneOffsetInMinutes();
		$offset = 0;
		$cacheKey = "DashboardDays-last-{$days}-days-offset-{$offset}min";
		
		$data = Cache::getOrSet($cacheKey, function() use ($days, $offset) {
			$datetime = new \DateTime(\Misc::userDate('Y-m-d 00:00:00'));
			$datetime->modify("-{$days} day");
			
			$query = new Select();
			$query->from('crash_archive', 'a')
				->field('DATE(a.created_at)', 'time')
				->field('COUNT(*)', 'total')
				->where('a.created_at', '>=', $datetime->format('Y-m-d H:i:s'))
				
			->orderBy(1, 'asc')
			->groupBy(1);
			
			$records = $query->fetchAllObj();
			$data = array();
			foreach ($records as $r) {
				$data[$r->time] = (int) $r->total;
			}
			return $data;
		}, (3720 - date('i')*60 + date('s')));
		
		// get data in last 30 days per package per date
		/*
		$query = new Select();
		$query
			->from('crash_archive', 'a')
			->field('DATE(a.created_at)', 'date')
			->field('a.package_id')
			->field('COUNT(*)', 'total')
			
			->innerJoin('package p', 'p.id', '=', 'a.package_id')
			->field('p.name', 'package_name')
			
			->where('a.created_at', '>=', $datetime->format('Y-m-d H:i:s'))
			->groupBy(2)
			->groupBy(1)
			->orderBy(1)
			->orderBy(2)
			->orderBy(4, 'DESC');
		
		$records = $query->fetchAllObj();
		$apps = $appName = array();
		foreach ($records as $r) {
			if (!isset($apps[$r->package_id])) {
				$apps[$r->package_id] = array();
				$appName[$r->package_id] = $r->package_name;
			}
			
			$apps[$r->package_id][$r->date] = (int) $r->total;
		}
		*/
		
		$start = new \DateTime(gmdate('Y-m-d'));
		$days--;
		$start->modify("-{$days} day");
		
		$serieData = $appsData = array();
		$today = \Misc::userDate('Y-m-d');
		
		do {
			$pointer = $start->format('Y-m-d');
			$serieData[$pointer] = isset($data[$pointer]) ? $data[$pointer] : 0;
			
// 			foreach ($apps as $packageId => $dates) {
// 				if (!isset($apps[$packageId][$pointer])) {
// 					$apps[$packageId][$pointer] = 0;
// 				}
// 			}
			
			$start->modify('+1 day');
		} while ($pointer < $today);
		
		$this->addSerie('total requests', array_values($serieData));
		
		/*foreach ($apps as $packageId => $dates) {
			if (sizeof($this->series) < 6) {
				ksort($dates);
				$this->addSerie($appName[$packageId], array_values($dates));
			}
		}*/
		
		foreach (array_keys($serieData) as $key) {
			$date = new \DateTime($key);
			$this->addOnXAxis($date->format('jS'));
		}
	}

}