<?php namespace Package;

use Koldy\Db\Model;

class Version extends Model {
	
	/**
	 * Get the package version ID
	 * @param string|int $package Package name or packageId from database
	 * @param string $version
	 * @return int
	 */
	public static function getId($package, $version) {
		if (!is_numeric($package)) {
			$packageId = \Package::getId($package);
		} else {
			$packageId = (int) $package;
		}
		
		$r = static::fetchOne(array(
			'package_id' => $packageId,
			'value' => $version
		));
		
		if ($r === false) {
			$r = static::create(array(
				'package_id' => $packageId,
				'value' => $version
			));
		}
		
		return $r->id;
	}
	
	/**
	 * Recalculate
	 */
	public static function recalculate() {
		static::getAdapter()->query('
			INSERT INTO package_version (id, total)

				SELECT
					a.package_version_id,
					COUNT(*) as total
				FROM
					crash_archive a
					INNER JOIN package_version p ON p.id = a.package_version_id
				GROUP BY 1
			
			ON DUPLICATE KEY UPDATE total = VALUES(total)
		');
	}
}