<?php namespace Chart;

use Highcharts\Pie\Serie;
use Koldy\Db\Select;
use Koldy\Db\Expr;
use Koldy\Cache;

class ProblematicOsVersions extends \Highcharts\Pie {
	
	/**
	 * Get number of requests per minute in last X minutes
	 * @param int $minutesFrom default -60 (last hour)
	 * @return array
	 */
	private static function getRecords($minutesFrom = -60) {
		$cacheKey = "ProblematicOsVersions-getRecords{$minutesFrom}";
		return Cache::getOrSet($cacheKey, function() use ($minutesFrom) {
			$datetime = new \DateTime(gmdate('Y-m-d H:i:00'));
			$datetime->modify("{$minutesFrom} minute");
			
			$query = new \Koldy\Db\Select();
			$query->from('crash_submit')
				->field(\Db::expr('CONCAT(os, \' \', android_version)'), 'os_version')
				->field(\Db::expr('COUNT(*)'), 'total')
				->where('created_at', '>=', $datetime->format('Y-m-d H:i:00'))
				->orderBy(2, 'desc')
				->groupBy(1);
			
			$tmp = $query->fetchAllObj();
			$data = array();
			foreach ($tmp as $r) {
				$data[$r->os_version] = (int) $r->total;
			}
			return $data;
		}, 65 - date('s'));
	}
	
	public function __construct($minutes) {
		$data = static::getRecords($minutes * -1);
		$serie = Serie::create('label');
		
		$total = 0; $cnt = 0; $other = 0;
		foreach ($data as $label => $count) {
			if ($cnt++ < 6) {
				$serie->addData($label, $count);
				$total += $count;
			} else {
				$other += $count;
				$total += $count;
			}
		}
		
		if ($other > 0) {
			$serie->addData('other', $other);
		}
		
		$this->title('Total items: ' . sizeof($data));
		
		$this->addSerie($serie);
	}
	
}