<?php namespace Stack\Trace\ResultSet;

use Koldy\Db\ResultSet;
use Koldy\Db\Select;

class Brand extends ResultSet {

	
	/**
	 * Set the brand ID
	 * @param int $brandId
	 */
	public function setbrandId($brandId) {
		$this->from('crash_archive', 'a')
			->field('a.stack_trace_id')
			->field('COUNT(*)', 'total')
			
			->innerJoin('stack_trace st', 'st.id', '=', 'a.stack_trace_id')
			->field('st.summary')
			
			->where('a.brand_id', $brandId)
			->groupBy('a.stack_trace_id');
		
		$query = new Select();
		$query->from('crash_archive', 'a')
			->field('a.stack_trace_id')
			->where('a.brand_id', $brandId)
			->innerJoin('stack_trace st', 'st.id', '=', 'a.stack_trace_id')
			->groupBy(1);
		
		$this->setCountQuery($query);
	}
	
	public function count() {
		return sizeof($this->getCountQuery()->fetchAll());
	}
	
}