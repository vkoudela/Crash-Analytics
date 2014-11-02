<?php

use Koldy\Db\Model;

class Status extends Model {
	
	protected static $primaryKey = 'name';
	
	/**
	 * Get the value or return null
	 * @param string $key
	 * @return string|null
	 */
	protected static function getValue($key) {
		$r = static::fetchOne($key);
		if ($r === false) {
			return null;
		} else {
			return $r->value;
		}
	}
	
	/**
	 * Set the value
	 * @param string $key
	 * @param string $value
	 */
	protected static function setValue($key, $value) {
		$r = static::fetchOne($key);
		if ($r === false) {
			static::create(array('name' => $key, 'value' => $value));
		} else {
			$r->value = $value;
			$r->save();
		}
	}
	
	/**
	 * Set last calculation datetime point so we can now from where to start calculate next time
	 * @param string $datetime
	 */
	public static function setLastCalculationDatetimePoint($datetime) {
		static::setValue('last_calculation_datetime_point', $datetime);
	}
	
	/**
	 * Get the last calculation date time
	 * @return string|null
	 */
	public static function getLastCalculationDatetimePoint() {
		return static::getValue('last_calculation_datetime_point');
	}
	
	/**
	 * Set last calculation time start
	 * @param string $datetime
	 */
	public static function setLastCalculationProcessStart($datetime) {
		static::setValue('last_calculation_process_start', $datetime);
	}
	
	/**
	 * Get the last calculation date time
	 * @return string|null
	 */
	public static function getLastCalculationProcessStart() {
		return static::getValue('last_calculation_process_start');
	}
	
	/**
	 * Set last calculation time end
	 * @param string $datetime
	 */
	public static function setLastCalculationProcessEnd($datetime) {
		static::setValue('last_calculation_process_end', $datetime);
	}
	
	/**
	 * Get the last calculation date time
	 * @return string|null
	 */
	public static function getLastCalculationProcessEnd() {
		return static::getValue('last_calculation_process_end');
	}
	
	/**
	 * Is calculation in progress
	 * @return boolean
	 */
	public static function isCalculationInProgress() {
		$r = static::fetchOne('calculation_in_progress');
		return ($r !== false);
	}
	
	/**
	 * Calculation has started
	 */
	public static function calculationStarted() {
		\Log::info('Calculation started');
		static::setValue('calculation_in_progress', gmdate('Y-m-d H:i:s'));
		static::setLastCalculationProcessStart(gmdate('Y-m-d H:i:s'));
	}
	
	/**
	 * Calculation has finished
	 */
	public static function calculationFinished($recordsProcessed) {
		$r = static::fetchOne('calculation_in_progress');
		if ($r !== false) {
			static::delete('calculation_in_progress');
		}
		
		static::setCalculationStatus("Done processing {$recordsProcessed} record(s)");
		static::setLastCalculationProcessEnd(gmdate('Y-m-d H:i:s'));
	}
	
	/**
	 * Set the calculation status
	 * @param string $status
	 */
	public static function setCalculationStatus($status) {
		\Log::info($status);
		static::setValue('calculation_status', $status);
	}
	
	/**
	 * Get the calculation status
	 * @return string|null
	 */
	public static function getCalculationStatus() {
		return static::getValue('calculation_status');
	}
	
	/**
	 * Trigger calculation termination
	 */
	public static function terminateCalculation($terminateOrClear = true) {
		if (!$terminateOrClear) {
			static::delete(array(
				'name' => 'terminate_calculation'
			));
			return;
		}
		
		static::setValue('terminate_calculation', gmdate('Y-m-d H:i:s'));
	}
	
	/**
	 * Should calculation be terminated or not?
	 * @return boolean
	 */
	public static function shouldCalcuationTerminate() {
		$r = static::fetchOne('terminate_calculation');
		if ($r === false) {
			return false;
		}
		
		return $r->value;
	}
}