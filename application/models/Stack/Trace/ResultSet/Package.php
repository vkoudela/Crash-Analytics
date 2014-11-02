<?php namespace Stack\Trace\ResultSet;

use Koldy\Db\ResultSet;
use Koldy\Db\Select;

class Package extends ResultSet {

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
			case 'day': $now->modify('-1 day'); return $now->format('Y-m-d H:i:s'); break;
			case 'week': $now->modify('-1 week'); return $now->format('Y-m-d H:i:s'); break;
			case '2-weeks': $now->modify('-2 week'); return $now->format('Y-m-d H:i:s'); break;
			case 'month': $now->modify('-1 month'); return $now->format('Y-m-d H:i:s'); break;
			case '2-months': $now->modify('-2 month'); return $now->format('Y-m-d H:i:s'); break;
		}
	}
	
	/**
	 * Set the package ID
	 * @param int $packageId
	 */
	public function setPackageId($packageId, $fromTime = null) {
		$this->from('crash_archive', 'a')
			->field('a.stack_trace_id')
			->field('COUNT(*)', 'total')
			
			->innerJoin('stack_trace st', 'st.id', '=', 'a.stack_trace_id')
			->field('st.summary')
			
			->where('a.package_id', $packageId)
			->groupBy('a.stack_trace_id');
		
		$query = new Select();
		$query->from('crash_archive', 'a', 'stack_trace_id')
			->where('a.package_id', $packageId)
			->innerJoin('stack_trace st', 'st.id', '=', 'a.stack_trace_id')
			->groupBy(1);
		
		if ($fromTime !== null) {
			$from = $this->getPeriodFrom($fromTime);
			$this->where('a.created_at', '>=', $from);
			$query->where('a.created_at', '>=', $from);
		}
		
		$this->setCountQuery($query);
	}
	
	public function count() {
		return sizeof($this->getCountQuery()->fetchAll());
	}
	
}