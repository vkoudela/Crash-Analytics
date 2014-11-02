<?php

use Koldy\Db\Model;

class Brand extends Model {
	
	/**
	 * Get the brand ID by brand name
	 * @param string $brandName
	 * @return int
	 */
	public static function getId($brandName) {
		if (($brand = static::fetchOne('name', $brandName)) === false) {
			$brand = static::create(array(
				'name' => $brandName
			));
		}
			
		return $brand->id;
	}
	
	/**
	 * Get options for select
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
	 * Recalculate
	 */
	public static function recalculate() {
		static::getAdapter()->query('
			INSERT INTO brand (id, total)

				SELECT
					a.brand_id,
					COUNT(*) as total
				FROM
					crash_archive a
					INNER JOIN brand b ON b.id = a.brand_id
				GROUP BY 1
			
			ON DUPLICATE KEY UPDATE total = VALUES(total)
		');
	}
}