<?php

use Koldy\Db\Model;

class Version extends Model {
	
	/**
	 * Get the OS version
	 * @param string $os 'Android' or something like that
	 * @param string $version
	 * @return int
	 */
	public static function getId($os, $version) {
		$r = static::fetchOne(array(
			'os' => $os,
			'name' => $version
		));
		
		if ($r === false) {
			$r = static::create(array(
				'os' => $os,
				'name' => $version
			));
		}
		
		return $r->id;
	}
	
	/**
	 * Get options for select
	 * @param bool $includeEmpty
	 * @return array
	 */
	public static function getSelectOptions($includeEmpty = false) {
		$records = static::query()
			->orderBy('os', 'asc')
			->orderBy('name', 'asc')
			->fetchAllObj();
		
		$data = array();
		foreach ($records as $r) {
			$data[$r->id] = "{$r->os} {$r->name}";
		}
		
		if ($includeEmpty) {
			$a = array('0' => '');
			foreach ($data as $key => $value) {
				$a[$key] = $value;
			}
			return $a;
		} else {
			return $data;
		}
	}

	/**
	 * Recalculate
	 */
	public static function recalculate() {
		static::getAdapter()->query('
			INSERT INTO version (id, total)
	
				SELECT
					a.os_version_id,
					COUNT(*) as total
				FROM
					crash_archive a
					INNER JOIN version v ON v.id = a.os_version_id
				GROUP BY 1
	
			ON DUPLICATE KEY UPDATE total = VALUES(total)
		');
	}
}