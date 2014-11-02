<?php namespace Phone;

class Model extends \Koldy\Db\Model {
	
	/**
	 * Get the Phone Model id from database
	 * @param string|int $brand
	 * @param string $phoneModel
	 * @return int
	 */
	public static function getId($brand, $phoneModel) {
		$brandId = is_numeric($brand)
			? (int) $brand
			: \Brand::getId($brand);
		
		$model = static::fetchOne(array(
			'brand_id' => $brandId,
			'name' => $phoneModel
		));
		
		if ($model === false) {
			$model = static::create(array(
				'brand_id' => $brandId,
				'name' => $phoneModel
			));
		}
		
		return $model->id;
	}
	
	/**
	 * Recalculate
	 */
	public static function recalculate() {
		static::getAdapter()->query('
			INSERT INTO phone_model (id, total)

				SELECT
					a.model_id,
					COUNT(*) as total
				FROM
					crash_archive a
					INNER JOIN phone_model p ON p.id = a.model_id
				GROUP BY 1
			
			ON DUPLICATE KEY UPDATE total = VALUES(total)
		');
	}
}