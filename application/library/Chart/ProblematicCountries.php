<?php namespace Chart;

use Highcharts\Pie\Serie;
use Koldy\Db\Select;
use Koldy\Db\Expr;
use Koldy\Cache;

class ProblematicCountries extends \Highcharts\Pie {
	
	/**
	 * Get number of requests per minute in last X minutes
	 * @param int $minutesFrom default -60 (last hour)
	 * @return array
	 */
	private static function getProblematicCountries($minutesFrom = -60) {
		$cacheKey = "getProblematicCountries{$minutesFrom}";
		return Cache::getOrSet($cacheKey, function() use ($minutesFrom) {
			$datetime = new \DateTime(gmdate('Y-m-d H:i:00'));
			$datetime->modify("{$minutesFrom} minute");
			
			$query = new \Koldy\Db\Select();
			$query->from('crash_submit')
				->field('country')
				->field(\Db::expr('COUNT(*)'), 'total')
				->where('created_at', '>=', $datetime->format('Y-m-d H:i:00'))
				->orderBy(2, 'desc')
				->groupBy(1);
			
			$tmp = $query->fetchAllObj();
			$data = array();
			foreach ($tmp as $r) {
				if ($r->country === null) {
					$name = 'unknown';
				} else {
					$country = \Country::fetchOne(array('tld' => $r->country));
					if ($country !== false) {
						$name = $country->country;
					} else {
						$name = 'unknown';
					}
				}
				$data[$name] = (int) $r->total;
			}
			return $data;
		}, 65 - date('s'));
	}
	
	public function __construct($minutes) {
		$data = static::getProblematicCountries($minutes * -1);
		$serie = Serie::create('app');
		
		$total = 0; $cnt = 0; $other = 0; $unknown = false;
		foreach ($data as $app => $count) {
			if ($app == 'unknown') {
				$unknown = true;
			}
			
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
		
		$minusUnknown = ($unknown) ? -1 : 0;
		$this->title('Total countries: ' . (sizeof($data) + $minusUnknown));
		
		$this->addSerie($serie);
	}
	
}