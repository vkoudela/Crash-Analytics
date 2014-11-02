<?php

use Koldy\Db\Model;

class Package extends Model {
	
	/**
	 * Get the package ID by package name
	 * @param string $packageName
	 * @return int
	 */
	public static function getId($packageName) {
		if (($package = static::fetchOne('name', $packageName)) === false) {
			$package = Package::create(array('name' => $packageName));
		}
		
		return $package->id;
	}
	
	/**
	 * Get select options
	 * @param bool $includeEmpty
	 * @return array
	 */
	public static function getSelectOptions($includeEmpty = false) {
		if ($includeEmpty) {
			$a = array('0' => '');
			foreach (static::fetchKeyValue('id', 'name', null, 'name', 'asc') as $key => $value) {
				$a[$key] = $value;
			}
			return $a;
		} else {
			return static::fetchKeyValue('id', 'name', null, 'name', 'asc');;
		}
	}
	
	/**
	 * Calculate stats
	 * @param string $from
	 * @param string $to
	 */
	public static function calculateStats($from = null, $to = null) {
		$where = '';
		
		if ($from !== null) {
			$where .= " AND created_at >= '{$from}'";
		}
		
		if ($to !== null) {
			$where .= " AND created_at <= '{$to}'";
		}
		
		$sql = "
			INSERT INTO package_stats (
				SELECT
					package_id,
					CONCAT(DATE(created_at), ' ', IF(HOUR(created_at) < 10, CONCAT('0', HOUR(created_at)), HOUR(created_at)), ':00:00') as time,
					COUNT(*) as total
				FROM
					crash_archive
				WHERE 1{$where}
				GROUP BY 1, 2
				ORDER BY 2 ASC, 1 ASC
			)
		";
		
		static::getAdapter()->query($sql);
	}
	
	/**
	 * Recalculate
	 */
	public static function recalculate() {
		static::getAdapter()->query('
			INSERT INTO package (id, total)

				SELECT
					a.package_id,
					COUNT(*) as total
				FROM
					crash_archive a
					INNER JOIN package p ON p.id = a.package_id
				GROUP BY 1
			
			ON DUPLICATE KEY UPDATE total = VALUES(total)
		');
	}
}