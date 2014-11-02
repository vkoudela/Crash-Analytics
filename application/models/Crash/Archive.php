<?php namespace Crash;

use Crash\Archive\Meta as Meta;
use Crash\Archive\Meta\Unknown as UnknownMeta;
use Koldy\Db\Model;
use Koldy\Log;

class Archive extends Model {
	
	private $brand = null;
	
	private $model = null;
	
	private $product = null;
	
	private $package = null;
	
	private $packageVersion = null;
	
	private $osVersion = null;
	
	/**
	 * Get the dependency
	 * @param string $class
	 * @param string $primaryValue
	 * @param string $returnValue
	 * @return string|NULL
	 */
	private function getDependency($class, $primaryValue, $returnValue = null) {
		$e = $class::fetchOne($primaryValue);
		if ($e === false) {
			return null;
		}
		
		if ($returnValue === null) {
			return $e;
		} else {
			return $e->$returnValue;
		}
	}
	
	/**
	 * Get package
	 * @return \Package
	 */
	public function getPackage() {
		return $this->getDependency('\Package', $this->package_id);
	}
	
	/**
	 * Get package name
	 * @return string|null
	 */
	public function getPackageName() {
		return $this->getDependency('\Package', $this->package_id, 'name');
	}
	
	/**
	 * Get brand name
	 * @return \Brand
	 */
	public function getBrand() {
		return $this->getDependency('\Brand', $this->brand_id);
	}
	
	/**
	 * Get brand name
	 * @return string
	 */
	public function getBrandName() {
		return $this->getDependency('\Brand', $this->brand_id, 'name');
	}
	
	/**
	 * Get phone model name
	 * @return \Phone\Model
	 */
	public function getPhoneModel() {
		return $this->getDependency('\Phone\Model', $this->model_id);		
	}
	
	/**
	 * Get phone model name
	 * @return string
	 */
	public function getPhoneModelName() {
		return $this->getDependency('\Phone\Model', $this->model_id, 'name');		
	}
	
	/**
	 * Get package version
	 * @return \Package\Version
	 */
	public function getPackageVersion() {
		return $this->getDependency('\Package\Version', $this->package_version_id);
	}
	
	/**
	 * Get package version name
	 * @return string|null
	 */
	public function getPackageVersionName() {
		return $this->getDependency('\Package\Version', $this->package_version_id, 'value');
	}
	
	/**
	 * Get phone product name
	 * @return \Product
	 */
	public function getProduct() {
		return $this->getDependency('\Product', $this->product_id);		
	}
	
	/**
	 * Get phone product name
	 * @return string
	 */
	public function getProductName() {
		return $this->getDependency('\Product', $this->product_id, 'name');		
	}
	
	/**
	 * Get OS version name
	 * @return \Version
	 */
	public function getOsVersion() {
		return $this->getDependency('\Version', $this->os_version_id);
	}
	
	/**
	 * Get OS version name
	 * @return string|null
	 */
	public function getOsVersionName() {
		return $this->getDependency('\Version', $this->os_version_id, 'name');
	}
	
	
	/**
	 * Insert archive metas
	 * @param array $metas
	 */
	public function insertMeta(array $metas) {
		
		$ok = array(
			'report_id','environment','build','settings_global','settings_system','settings_secure','device_features',
			'shared_preferences', 'initial_configuration','crash_configuration','dumpsys_meminfo','display',
			'stack_trace','logcat','tktal_mem_size','@evice_features','installation_id','file_path',
			'dropbox','is_silent','custom_data'
		);
		
		foreach ($metas as $key => $value) {
			if (in_array($key, $ok)) {
				// first, insert into meta_archive to get last ID
				try {
					$meta = Meta::create(array(
						'crash_id' => $this->id,
						'name' => $key,
						'value' => $value
					));
				} catch (\Exception $e) {
					Log::error("Can not insert meta for={$this->id} key={$key} value={$value}");
					Log::exception($e);
					\Status::setCalculationStatus('Died. Failed on meta table');
					exit(1);
				}
			} else {
				// key is not recognized, but we'll still write it in database
				try {
					UnknownMeta::create(array(
						'report_id' => $this->id,
						'meta_name' => $key,
						'meta_value' => $value
					));
				} catch (\Exception $e) {
					Log::error("Can not insert unknown meta report_id={$this->id} meta_name={$key} meta_value={$value}");
					Log::exception($e);
					\Status::setCalculationStatus('Died. Failed on unknown meta table');
					exit(1);
				}
			}
		}
	}
	
	/**
	 * Get the meta value
	 * @param string $metaName
	 * @return mixed
	 */
	public function getMeta($metaName) {
		$records = Meta::fetch(array(
			'crash_id' => $this->id,
			'name' => $metaName
		), array('value'));
		
		if (sizeof($records) > 0) {
			return $records[0]->value;
		}
		
		return null;
	}
	
	/**
	 * Get metas from db
	 * @return array
	 */
	public function getMetas() {
		$records = Meta::fetch(array(
			'crash_id' => $this->id
		), array('name', 'value'));
		
		$metas = array();
		foreach ($records as $r) {
			$metas[$r->name] = $r->value;
		}
		
		return $metas;
	}
	
	/**
	 * Get metas from db
	 * @return array
	 */
	public function getUnknownMetas() {
		$records = Meta\Unknown::fetch(array(
			'report_id' => $this->id
		), array('meta_name', 'meta_value'));
		
		$metas = array();
		foreach ($records as $r) {
			$metas[$r->meta_name] = $r->meta_value;
		}
		
		return $metas;
	}
}