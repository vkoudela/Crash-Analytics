<?php namespace Package\ResultSet;

use Koldy\Db\ResultSet;
use Koldy\Db\Select;

class Version extends ResultSet {

	private $fromTime = null;

	/**
	 * Set the from period
	 * @param string $last
	 * @return string
	 */
	private function getPeriodFrom($last) {
		$now = new \DateTime(gmdate('Y-m-d H:i:s'));
		switch ($last) {
			default: return $last; break;
			case '12-hour': $now->modify('-12 hour'); return $now->format('Y-m-d H:i:s'); break;
			case 'day': $now->modify('-1 day'); return $now->format('Y-m-d H:00:00'); break;
			case 'week': $now->modify('-1 week'); return $now->format('Y-m-d 00:00:00'); break;
			case '2-weeks': $now->modify('-2 week'); return $now->format('Y-m-d 00:00:00'); break;
			case 'month': $now->modify('-1 month'); return $now->format('Y-m-d 00:00:00'); break;
			case '2-months': $now->modify('-2 month'); return $now->format('Y-m-d 00:00:00'); break;
		}
	}

	/**
	 * Set the package ID
	 * @param int $packageId
	 * @param string $fromTime
	 */
	public function setPackageId($packageId, $fromTime = null) {
		$this->from('crash_archive', 'a')
			->field('a.package_version_id')
			->field('COUNT(*)', 'total')
			
			->innerJoin('package_version pv', 'pv.id', '=', 'a.package_version_id')
			->field('pv.value', 'name')
			
			->where('a.package_id', $packageId)
			->groupBy('a.package_version_id');
		
		$query = new Select();
		$query->from('crash_archive', 'a', 'package_version_id')
			->where('a.package_id', $packageId)
			->innerJoin('package_version pv', 'pv.id', '=', 'a.package_version_id')
			->groupBy(1);
		
		if ($fromTime !== null) {
			$this->fromTime = $from = $this->getPeriodFrom($fromTime);
			$this->where('a.created_at', '>=', $from);
			$query->where('a.created_at', '>=', $from);
		}
		
		$this->setCountQuery($query);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Db\ResultSet::count()
	 */
	public function count() {
		return sizeof($this->getCountQuery()->fetchAll());
	}
	
	/**
	 * Get the time from
	 * @return string
	 */
	public function getFromTime() {
		return $this->fromTime;
	}
}