<?php

use Koldy\Db\Model;

class Country extends Model {
	
	/**
	 * Get options for select
	 * @param bool $includeEmpty
	 * @return array
	 */
	public static function getSelectOptions($includeEmpty = false) {
		if ($includeEmpty) {
			$a = array('0' => '');
			foreach (static::fetchKeyValue('id', 'country', null, 'country', 'asc') as $key => $value) {
				$a[$key] = $value;
			}
			return $a;
		} else {
			return static::fetchKeyValue('id', 'country', null, 'country', 'asc');
		}
	}

	/**
	 * Recalculate totals
	 */
	public static function recalculate() {
		static::getAdapter()->query('
			INSERT INTO country (id, total)

				SELECT
					country_id,
					COUNT(*) as total
				FROM
					crash_archive a
					INNER JOIN country c ON c.id = a.country_id
				GROUP BY 1
			
			ON DUPLICATE KEY UPDATE total = VALUES(total)
		');
	}
}
