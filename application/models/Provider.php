<?php

use Koldy\Db\Model;

class Provider extends Model {
	
	/**
	 * Get the provider ID
	 * @param string $name
	 * @return int
	 */
	public static function getId($name) {
		if ($name === null || trim($name) == '') {
			return null;
		}
		
		if (($provider = static::fetchOne(array('name' => $name))) === false) {
			$provider = static::create(array('name' => $name));
		}
		
		return $provider->id;
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
			INSERT INTO provider (id, total)

				SELECT
					a.provider_id,
					COUNT(*) as total
				FROM
					crash_archive a
					INNER JOIN provider p ON p.id = a.provider_id
				GROUP BY 1
			
			ON DUPLICATE KEY UPDATE total = VALUES(total)
		');
	}

	public static function normalizeHostName($hostName) {
		if ($hostName === null) {
			return null;
		}

		$hostName = explode('.', $hostName);
		$size = sizeof($hostName);

		switch ($size) {
			case 1:
			case 2:
				return implode('.', $hostName);
				break;
			
			default:
				$third = $hostName[$size -3];
				if (preg_match('^([0-9]{1,3}\-[0-9]{1,3}\-[0-9]{1,3}\-[0-9]{1,3})^', $third)
					|| preg_match('^([0-9]{1,3}\-).*^', $third)
					|| preg_match('([0-9]{1,})', $third)) {
					return "{$hostName[$size -2]}.{$hostName[$size -1]}"; 
				} else {
					return "{$hostName[$size -3]}.{$hostName[$size -2]}.{$hostName[$size -1]}";
				}
				
				break;
		}
	}
}