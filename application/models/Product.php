<?php

use Koldy\Db\Model;

class Product extends Model {
	
	/**
	 * Get the product ID from database
	 * @param string|int $brand
	 * @param string $productName
	 * @return int
	 */
	public static function getId($brand, $productName) {
		$brandId = is_numeric($brand)
			? (int) $brand
			: \Brand::getId($brand);
		
		$product = static::fetchOne(array(
			'brand_id' => $brandId,
			'name' => $productName
		));
		
		if ($product === false) {
			$product = static::create(array(
				'brand_id' => $brandId,
				'name' => $productName
			));
		}
		
		return $product->id;
	}
	
	public static function recalculate() {
		static::getAdapter()->query('
			INSERT INTO product (id, total)

				SELECT
					a.product_id,
					COUNT(*) as total
				FROM
					crash_archive a
					INNER JOIN product p ON p.id = a.product_id
				GROUP BY 1
			
			ON DUPLICATE KEY UPDATE total = VALUES(total)
		');
	}
	
}