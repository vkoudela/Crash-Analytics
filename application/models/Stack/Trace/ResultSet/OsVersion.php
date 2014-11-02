<?php namespace Stack\Trace\ResultSet;

use Koldy\Db\ResultSet;
use Koldy\Db\Select;

class OsVersion extends ResultSet {

	
	/**
	 * Set the os_version ID
	 * @param int $osVersionId
	 */
	public function setOsVersionId($osVersionId) {
		$this->from('crash_archive', 'a')
			->field('a.stack_trace_id')
			->field('COUNT(*)', 'total')
			
			->innerJoin('stack_trace st', 'st.id', '=', 'a.stack_trace_id')
			->field('st.summary')
			
			->where('a.os_version_id', $osVersionId)
			->groupBy('a.stack_trace_id');
		
		$query = new Select();
		$query->from('crash_archive', 'a', 'stack_trace_id')
			->where('a.os_version_id', $osVersionId)
			->innerJoin('stack_trace st', 'st.id', '=', 'a.stack_trace_id')
			->groupBy(1);
		
		$this->setCountQuery($query);
	}
	
	public function count() {
		return sizeof($this->getCountQuery()->fetchAll());
	}
	
}