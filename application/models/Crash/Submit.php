<?php namespace Crash;

use Koldy\Db\Model;
use Koldy\Log;
use Koldy\Json;
use Koldy\Request;
use Koldy\Db;
use Koldy\Db\Select;
use Koldy\Cache;

class Submit extends Model {
	
	/**
	 * Insert meta for crash submit
	 * @param string $name ('stack_trace','logcat','settings_system','settings_secure',...)
	 * @param string $value
	 * @return \Crash\Submit\Meta
	 */
	public function insertMeta($name, $value) {
		return Submit\Meta::create(array(
			'submit_id' => $this->id,
			'meta_name' => $name,
			'meta_value' => $value
		));
	}
	
	/**
	 * Get the metas for this record
	 * @return array
	 */
	public function getMetas() {
		$query = new Select();
		$query->from('crash_submit_meta')
			->field('meta_name')
			->field('meta_value')
			->where('submit_id', $this->id);
		
		$metas = array();
		foreach ($query->fetchAllObj() as $r) {
			$metas[$r->meta_name] = $r->meta_value;
		}
		
		return $metas;
	}
	
	/**
	 * Get the meta value
	 * @param string $name
	 * @return string|null
	 */
	public function getMeta($name) {
		$query = new Select();
		$query->from('crash_submit_meta')
			->field('meta_value')
			->where('submit_id', $this->id)
			->where('meta_name', $name);
		
		$records = $query->fetchAllObj();
		if (sizeof($records) == 1) {
			return $records[0]['meta_value'];
		}
		
		return null;
	}
	
	/**
	 * Process the request
	 * @param string $os 'Android','iOS' or 'Windows'
	 *
	 * TODO: This logic shouldn't be in the model
	 */
	public static function processRequest($os) {
		if (!isset($_POST['PACKAGE_NAME'])) {
			Log::warning('Package name is not set! UAS=' . Request::userAgent() . ' POST=' . Json::encode($_POST));
		}
		
		$time = time();
		
		$ip = $_SERVER['REMOTE_ADDR'];
		$host = gethostbyaddr($ip);
		$country = null;
		$provider = null;
		
		if ($host != $ip && strpos($host, '.') !== false) {
			$country = strtolower(substr($host, strrpos($host, '.') +1));
			if (strlen($country) != 2) {
				$country = null;
			}
				
			$provider = \Provider::normalizeHostName($host);
		}
		
		Log::notice("Got request time={$time} host={$host} country={$country}");
		
		$values = array(
			'created_at',
			'package_name',
			'app_version_code',
			'app_version_name',
			'brand',
			'phone_model',
			'product',
			'stack_trace',
			'android_version',
			'file_path',
			'total_mem_size',
			'available_mem_size',
			'user_comment',
			'user_app_start_date',
			'user_crash_date',
			'installation_id',
			'report_id',
			'user_email'
		);
		
		$data = array();
		$vals = $_POST;
		foreach ($values as $key) {
			$key = strtoupper($key);
			if (isset($vals[$key])) {
				$data[$key] = trim($vals[$key]);
				unset($vals[$key]);
			}
		}
		
		if (isset($data['user_email']) && ($data['user_email'] == 'N/A' || trim($data['user_email']) == '')) {
			$data['user_email'] = null;
		}
		
		$data['created_at'] = gmdate('Y-m-d H:i:s');
		$data['country'] = $country;
		$data['provider'] = $provider;
		$data['os'] = $os;
		
		$secureCount = 0;
		do {
			$submit = static::create($data);
			if ($submit === false) {
				Db::getAdapter()->close();
				Log::info("Failed to insert crash submit report for time={$time}, waiting 5 seconds");
				set_time_limit(60);
				sleep(5);
				Db::getAdapter()->reconnect();
				Log::info("Retry {$secureCount} insert crash submit report for time={$time}");
			}
		} while ($submit === false && $secureCount++ < 3);
		
		if ($submit !== false) {
			foreach ($vals as $metaName => $metaValue) {
				if ($metaValue !== null) {
					$metaValue = trim($metaValue);
					if (strlen($metaValue) > 0) {
						$secureCount = 0;
						do {
							$dbMeta = $submit->insertMeta(strtolower($metaName), trim($metaValue));
							if ($dbMeta === false) {
								Db::getAdapter()->close();
								Log::info("Failed to insert submit meta for meta={$metaName} time={$time}, waiting 5 seconds");
								set_time_limit(60);
								sleep(5);
								Db::getAdapter()->reconnect();
								Log::info("Retry {$secureCount} meta insert for meta={$metaName} time={$time}");
							}
						} while ($dbMeta === false && $secureCount++ < 2);
					}
				}
			}
		}
		
		\Email\Trigger::processRequest($_POST);
		
		Log::notice("Done request time={$time}");
	}
	
	/**
	 * Get number of requests per minute in last X minutes
	 * @param int $minutesFrom default -60 (last hour)
	 * @return array
	 */
	public static function getRequestsPerMinute($minutesFrom = -60) {
		$cacheKey = "getRequestsPerMinute{$minutesFrom}";
		return Cache::getOrSet($cacheKey, function() use ($minutesFrom) {
			$datetime = new \DateTime(gmdate('Y-m-d H:i:s'));
			$datetime->modify("{$minutesFrom} minute");
			
			$query = new \Koldy\Db\Select();
			$query->from('crash_submit')
				->field(\Db::expr('MINUTE(created_at)'), 'time')
				->field(\Db::expr('COUNT(*)'), 'total')
				->where('created_at', '>=', $datetime->format('Y-m-d H:i:00'))
				->orderBy('created_at', 'asc')
				->groupBy(1);
			
			$tmp = $query->fetchAllObj();
			$data = array();
			foreach ($tmp as $r) {
				$data[$r->time] = (int) $r->total;
			}
			return $data;
		}, 65 - date('s'));
	}
	
	/**
	 * Get number of requests per minute in last X minutes
	 * @param int $minutesFrom default -60 (last hour)
	 * @return array
	 */
	public static function getProblematicApps($minutesFrom = -60) {
		$cacheKey = "getProblematicApps{$minutesFrom}";
		return Cache::getOrSet($cacheKey, function() use ($minutesFrom) {
			$datetime = new \DateTime(gmdate('Y-m-d H:i:s'));
			$datetime->modify("{$minutesFrom} minute");
			
			$query = new \Koldy\Db\Select();
			$query->from('crash_submit')
				->field('package_name')
				->field(\Db::expr('COUNT(*)'), 'total')
				->where('created_at', '>=', $datetime->format('Y-m-d H:i:s'))
				->orderBy(2, 'desc')
				->groupBy(1);
			
			$tmp = $query->fetchAllObj();
			$data = array();
			foreach ($tmp as $r) {
				$data[trim($r->package_name) == '' ? 'unknown' : $r->package_name] = (int) $r->total;
			}
			return $data;
		}, 65 - date('s'));
	}
	
	/**
	 * @param int $minutesFrom default -60 (last hour)
	 * @return array
	 */
	public static function getProblematicBrandModels($minutesFrom = -60) {
		$cacheKey = "getProblematicBrandModels{$minutesFrom}";
		return Cache::getOrSet($cacheKey, function() use ($minutesFrom) {
			$datetime = new \DateTime(gmdate('Y-m-d H:i:s'));
			$datetime->modify("{$minutesFrom} minute");
	
			$query = new \Koldy\Db\Select();
			$query->from('crash_submit')
				->field('brand')
				->field(Db::expr('COUNT(*)'), 'total')
				->where('created_at', '>=', $datetime->format('Y-m-d H:i:s'))
				->orderBy(2, 'desc')
				->groupBy(1);
			
			$tmp = $query->fetchAllObj();
			$data = array();
			foreach ($tmp as $r) {
				$data[trim($r->brand) == '' ? 'unknown' : $r->brand] = (int) $r->total;
			}
			return $data;
		}, 65 - date('s'));
	}
}