<?php

use Koldy\Db\Select;
use Koldy\Log;
use Koldy\Db;
use Crash\Submit as CrashSubmit;
use Crash\Archive as CrashArchive;
use Package\Version as PackageVersion;
use Phone\Model as PhoneModel;
use Version as OsVersion;
use Stack\Trace as StackTrace;

class Calculation {
	
	protected $brandIds = array();
	
	protected $packageIds = array();
	
	protected $packageVersionIds = array();
	
	protected $productIds = array();
	
	protected $osIds = array();
	
	protected $osVersionIds = array();
	
	protected $countryIds = array();
	
	protected $providerIds = array();
	
	protected $stackTraceIds = array();
	
	/**
	 * Get the package ID from database or create it in database
	 * @param string $package
	 * @return int
	 */
	private function getPackageId($package) {
		if ($package === null || trim($package) == '') {
			return null;
		}
		
		if (!isset($this->packageIds[$package])) {
			$this->packageIds[$package] = Package::getId($package);
		}
		
		return $this->packageIds[$package];
	}
	
	/**
	 * Get the package version ID
	 * @param int $package
	 * @param string $version
	 * @return int
	 */
	private function getPackageVersionId($package, $version) {
		if ($package === null || $version === null || trim($package) == '' || trim($version) == '') {
			return null;
		}
		
		$key = $package . $version;
		if (!isset($this->packageVersionIds[$key])) {
			$this->packageVersionIds[$key] = PackageVersion::getId($package, $version);
		}
		
		return $this->packageVersionIds[$key];
	}
	
	/**
	 * Get the brand ID
	 * @param string $brand
	 * @return int
	 */
	private function getBrandId($brand) {
		if ($brand === null || trim($brand) == '') {
			return null;
		}
		
		if (!isset($this->brandIds[$brand])) {
			$this->brandIds[$brand] = Brand::getId($brand);
		}
		
		return $this->brandIds[$brand];
	}
	
	/**
	 * Get the phone model ID
	 * @param string $brand
	 * @param string $phoneModel
	 * @return int
	 */
	private function getModelId($brand, $phoneModel) {
		if ($brand === null || $phoneModel === null || trim($brand) == '' || trim($phoneModel) == '') {
			return null;
		}
		
		$key = $brand . $phoneModel;
		if (!isset($this->brandIds[$key])) {
			$this->brandIds[$key] = PhoneModel::getId($brand, $phoneModel);
		}
		
		return $this->brandIds[$key];
	}
	
	/**
	 * Get the product ID
	 * @param string $brand
	 * @param string $product
	 * @return int
	 */
	private function getProductId($brand, $product) {
		if ($brand === null || $product === null || trim($brand) == '' || trim($product) == '') {
			return null;
		}
		
		$key = $brand . $product;
		if (!isset($this->productIds[$key])) {
			$this->productIds[$key] = Product::getId($brand, $product);
		}
		
		return $this->productIds[$key];
	}
	
	/**
	 * Get the OS version
	 * @param string|int $os
	 * @param string $version
	 * @return int
	 */
	private function getOsVersion($os, $version) {
		if ($os === null || $version === null || trim($os) == '' || trim($version) == '') {
			return null;
		}
		
		$key = $os . $version;
		if (!isset($this->osVersionIds[$key])) {
			$this->osVersionIds[$key] = OsVersion::getId($os, $version);
		}
		
		return $this->osVersionIds[$key];
	}
	
	/**
	 * Get the country ID
	 * @param string $tld
	 * @return int
	 */
	private function getCountryId($tld) {
		if ($tld === null || trim($tld) == '') {
			return null;
		}
		
		if (!isset($this->countryIds[$tld])) {
			if (($country = Country::fetchOne(array('tld' => $tld))) !== false) {
				$this->countryIds[$tld] = $country->id;
			} else {
				$this->countryIds[$tld] = null;
			}
		}
		
		return $this->countryIds[$tld];
	}
	
	/**
	 * Get the provider ID
	 * @param string $provider
	 * @return int
	 */
	private function getProviderId($provider) {
		if ($provider === null || trim($provider) == '') {
			return null;
		}
		
		if (!isset($this->providerIds[$provider])) {
			$this->providerIds[$provider] = Provider::getId($provider);
		}
		return $this->providerIds[$provider];
	}
	
	/**
	 * Get stack trace ID
	 * @param string $stackTraceSummary
	 * @param string $createdAt
	 * @return int
	 */
	private function getStackTraceId($stackTraceSummary, $createdAt = null) {
		if ($stackTraceSummary === null || trim($stackTraceSummary) == '') {
			return null;
		}
		
		$md5 = md5($stackTraceSummary);
		if (!isset($this->stackTraceIds[$md5])) {
			$this->stackTraceIds[$md5] = StackTrace::getId($stackTraceSummary, $createdAt);
		}
		
		return $this->stackTraceIds[$md5];
	}
	
	/**
	 * Calculate for reports
	 * @return boolean
	 */
	public function calculate() {
		if (Status::isCalculationInProgress()) {
			Log::info('Trying to start calculation, but calculation is already in progress. Last was started on ' . Status::getLastCalculationProcessStart(). ' UTC');
			return false;
		}
		
		ini_set('memory_limit', '512M');
		set_time_limit(0);
		
		Status::calculationStarted();
		Status::setCalculationStatus('Initializing');
		Log::info('Calculation started');
		
		/**
		 * metas: 'file_path', 'build', 'environment', 'settings_global', 'settings_system', 'settings_secure',
		 * 'device_features', 'shared_preferences', 'initial_configuration', 'crash_configuration',
		 * 'dumpsys_meminfo', 'display', 'stack_trace', 'logcat', 'tktal_mem_size', '@evice_features', 'installation_id'
		 */
		
		$query = new Select();
		$query->from('crash_submit')->field('id')->limit(0, 200000);
		
		$maxQuery = CrashArchive::query()
			->field('MAX(created_at)', 'time');
		
		$max = $maxQuery->fetchFirstObj();
		unset($maxQuery);
		
		$lastDatetime = new DateTime(gmdate('Y-m-d H:00:00'));
		$lastDatetime->modify('-1 hour');
		$query->where('created_at', '<', $lastDatetime->format('Y-m-d H:i:s'));
		
		$ids = $query->fetchAllObj();
		$sizeofIds = sizeof($ids);
		Log::info('Loaded ids: ' . $sizeofIds);
		
		$brandTotals
		= $countryTotals
		= $packageTotals
		= $packageVersionTotals
		= $phoneModelTotals
		= $productTotals
		= $providerTotals
		= $stackTraceTotals
		= $osVersionTotals
		= array();
		
		foreach ($ids as $index => $r) {

			// on every 25 records, we should ask ourself: is that it?
			if ($index % 25 == 0) {
				$shouldTerminate = Status::shouldCalcuationTerminate();
				if ($shouldTerminate !== false) {
					Log::info("Noticed request for calculation termination on {$shouldTerminate}. Aborted!");
					Status::setCalculationStatus('Terminated after ' . ($index +1) . ' records');
					Status::terminateCalculation(false);
					return false;
				}
			}
			
			$id = $r->id;
			
			// record by record
			Log::info("Will now fetch id={$id}");
			
			if ($index % 15 == 0) {
				$percent = round($index / $sizeofIds * 100, 2);
				Status::setCalculationStatus("Working; {$index}/{$sizeofIds} {$percent}%");
			}
			
			$submit = CrashSubmit::fetchOne($id);
			if ($submit !== false && $submit->package_name !== null && trim($submit->package_name !== null) != '') {
				$appStartTime = strtotime($submit->user_app_start_date);
				$appCrashTime = strtotime($submit->user_crash_date);
				$appLifetime = $appCrashTime - $appStartTime;
				
				$metas = $submit->getMetas();
				
				if ($submit->report_id !== null && trim($submit->report_id) !== '') {
					$metas['report_id'] = $submit->report_id;
				}
				
				if ($submit->file_path !== null && trim($submit->file_path) !== '') {
					$metas['file_path'] = $submit->file_path;
				}
				
				if ($submit->installation_id !== null && trim($submit->installation_id) !== '') {
					$metas['installation_id'] = $submit->installation_id;
				}
				
				$stackTrace = $submit->stack_trace;

				if ($stackTrace === null) {
					$stackTraceSummary = null;
				} else {
					$metas['stack_trace'] = $stackTrace;
					$stackTraceSummary = StackTrace::getSummary($stackTrace);
				}
				
				$packageId = $this->getPackageId($submit->package_name);
				$packageVersionId = $this->getPackageVersionId($this->getPackageId($submit->package_name), $submit->app_version_name);
				$brandId = $this->getBrandId($submit->brand);
				$phoneModelId = $this->getModelId($this->getBrandId($submit->brand), $submit->phone_model);
				$productId = $this->getProductId($this->getBrandId($submit->brand), $submit->product);
				$osVersionId = $this->getOsVersion($submit->os, $submit->android_version);
				$stackTraceId = $this->getStackTraceId($stackTraceSummary, $submit->created_at);
				$countryId = $this->getCountryId($submit->country);
				$providerId = $this->getProviderId($submit->provider);
				
				$archive = CrashArchive::create(array(
					'created_at' => $submit->created_at,
					'package_id' => $packageId,
					'package_version_id' => $packageVersionId,
					'brand_id' => $brandId,
					'model_id' => $phoneModelId,
					'product_id' => $productId,
					'os' => $submit->os,
					'os_version_id' => $osVersionId,
					'total_mem_size' => $submit->total_mem_size,
					'available_mem_size' => $submit->available_mem_size,
					'user_comment' => (trim($submit->user_comment) == '' ? null : trim($submit->user_comment)),
					'user_email' => (trim($submit->user_email) == 'N/A' ? null : trim($submit->user_email)),
					'user_app_start_date' => $submit->user_app_start_date,
					'user_crash_date' => $submit->user_crash_date,
					'user_app_lifetime' => $appLifetime,
					'stack_trace_id' => $stackTraceId,
					'country_id' => $countryId,
					'provider_id' => $providerId
				));
				
				$archive->insertMeta($metas);
				
				// prepare increments for totals
				
				if ($packageId !== null) {
					if (!isset($packageTotals[$packageId])) {
						$packageTotals[$packageId] = 0;
					}
					$packageTotals[$packageId]++;
				}
				
				if ($packageVersionTotals !== null) {
					if (!isset($packageVersionTotals[$packageVersionId])) {
						$packageVersionTotals[$packageVersionId] = 0;
					}
					$packageVersionTotals[$packageVersionId]++;
				}
				
				if ($brandId !== null) {
					if (!isset($brandTotals[$brandId])) {
						$brandTotals[$brandId] = 0;
					}
					$brandTotals[$brandId]++;
				}
				
				if ($phoneModelId !== null) {
					if (!isset($phoneModelTotals[$phoneModelId])) {
						$phoneModelTotals[$phoneModelId] = 0;
					}
					$phoneModelTotals[$phoneModelId]++;
				}
				
				if ($productId !== null) {
					if (!isset($productTotals[$productId])) {
						$productTotals[$productId] = 0;
					}
					$productTotals[$productId]++;
				}
				
				if ($osVersionTotals !== null) {
					if (!isset($osVersionTotals[$osVersionId])) {
						$osVersionTotals[$osVersionId] = 0;
					}
					$osVersionTotals[$osVersionId]++;
				}
				
				if ($stackTraceId !== null) {
					if (!isset($stackTraceTotals[$stackTraceId])) {
						$stackTraceTotals[$stackTraceId] = 0;
					}
					$stackTraceTotals[$stackTraceId]++;
				}
				
				if ($countryId !== null) {
					if (!isset($countryTotals[$countryId])) {
						$countryTotals[$countryId] = 0;
					}
					$countryTotals[$countryId]++;
				}
				
				if ($providerId !== null) {
					if (!isset($providerTotals[$providerId])) {
						$providerTotals[$providerId] = 0;
					}
					$providerTotals[$providerId]++;
				}
			}
		}
		
		if ($sizeofIds > 0) {
			Log::info('Calculation done');
			Status::setCalculationStatus('Starting to delete submit records');
			
			$deleteIds = array();
			foreach ($ids as $index => $r) {
				$deleteIds[] = $r->id;
				
				if (sizeof($deleteIds) == 100) {
					$percent = round($index / $sizeofIds * 100, 2);
					Status::setCalculationStatus("Deleting submit records {$index}/{$sizeofIds} {$percent}%");
					Db::delete('crash_submit_meta')->whereIn('submit_id', $deleteIds)->exec();
					Db::delete('crash_submit')->whereIn('id', $deleteIds)->exec();
					$deleteIds = array();
				}
			}
			
			if (sizeof($deleteIds) > 0) {
				Status::setCalculationStatus("Deleting submit records {$index}/{$sizeofIds} 100%");
				Db::delete('crash_submit_meta')->whereIn('submit_id', $deleteIds)->exec();
				Db::delete('crash_submit')->whereIn('id', $deleteIds)->exec();
			}
			
			Status::setCalculationStatus('Started totals calculations update!');
			
			// update calculated increments
			foreach ($brandTotals as $id => $total) {
				Status::setCalculationStatus('Brands: Updating calculated totals');
				Db::update('brand')->increment('total', $total)->where('id', $id)->exec();
			}
			
			foreach ($countryTotals as $id => $total) {
				Status::setCalculationStatus('Countries: Updating calculated totals');
				Db::update('country')->increment('total', $total)->where('id', $id)->exec();
			}
			
			foreach ($packageTotals as $id => $total) {
				Status::setCalculationStatus('Packages: Updating calculated totals');
				Db::update('package')->increment('total', $total)->where('id', $id)->exec();
			}
			
			foreach ($packageVersionTotals as $id => $total) {
				Status::setCalculationStatus('Package version: Updating calculated totals');
				Db::update('package_version')->increment('total', $total)->where('id', $id)->exec();
			}
			
			foreach ($phoneModelTotals as $id => $total) {
				Status::setCalculationStatus('Phone models: Updating calculated totals');
				Db::update('phone_model')->increment('total', $total)->where('id', $id)->exec();
			}
			
			foreach ($productTotals as $id => $total) {
				Status::setCalculationStatus('Products: Updating calculated totals');
				Db::update('product')->increment('total', $total)->where('id', $id)->exec();
			}
			
			foreach ($providerTotals as $id => $total) {
				Status::setCalculationStatus('Providers: Updating calculated totals');
				Db::update('provider')->increment('total', $total)->where('id', $id)->exec();
			}
			
			foreach ($stackTraceTotals as $id => $total) {
				Status::setCalculationStatus('Stack traces: Updating calculated totals');
				Db::update('stack_trace')->increment('total', $total)->where('id', $id)->exec();
			}
			
			foreach ($osVersionTotals as $id => $total) {
				Status::setCalculationStatus('OS version: Updating calculated totals');
				Db::update('version')->increment('total', $total)->where('id', $id)->exec();
			}
			
		} else {
// 			Log::info('Calculation done, no records processed');
		}
		
		Status::calculationFinished($sizeofIds);
		
		return true;
	}
	
}