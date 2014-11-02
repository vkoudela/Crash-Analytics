<?php namespace Koldy\Db;
/**
 * The ResultSet class needs to handle data sets fetched from database ready
 * for pagination and searching. Long story short, you can easily create
 * DataTables to work with this simply by passing the ResultSet instance.
 * 
 */
class ResultSet extends Select {


	/**
	 * @var \Koldy\Select
	 */
	protected $countQuery = null;


	/**
	 * The search string
	 * 
	 * @var string
	 */
	protected $searchTerm = null;


	/**
	 * The fields on which search will be performed - if not set, search
	 * will be performed on all fields
	 * 
	 * @var array
	 */
	protected $searchFields = null;


	/**
	 * Set the custom count query. If you're working with custom count query,
	 * then you must handle the search terms by yourself.
	 * 
	 * @param Select $query
	 * @return \Koldy\Db\ResultSet
	 */
	public function setCountQuery(Select $query) {
		$this->countQuery = $query;
		return $this;
	}


	/**
	 * Get SELECT query for total count
	 * 
	 * @return \Koldy\Db\Select
	 */
	protected function getCountQuery() {
		if ($this->countQuery !== null) {
			return $this->countQuery;
		}

		if ($this->searchTerm !== null && $this->searchFields === null) {
			$fields = $this->getFields();
			$searchFields = array();
			foreach ($fields as $field) {
				$searchFields[] = $field['name'];
			}
		} else {
			$searchFields = null;
		}

		$query = clone $this;
		$query->resetFields();
		$query->resetLimit();
		$query->resetOrderBy();
		$query->field('COUNT(*)', 'total');

		if ($searchFields !== null) {
			$query->setSearchFields($searchFields);
		}

		return $query;
	}


	/**
	 * Set search fields
	 * 
	 * @param array $fields
	 * @return \Koldy\Db\ResultSet
	 */
	public function setSearchFields(array $fields) {
		$this->searchFields = $fields;
		return $this;
	}


	/**
	 * Search for the fields
	 * 
	 * @param string $searchText
	 * @return \Koldy\Db\ResultSet
	 */
	public function search($searchText) {
		$this->searchTerm = $searchText;
		return $this;
	}


	/**
	 * Count results
	 * 
	 * @return int
	 */
	public function count() {
		$result = $this->getCountQuery()->fetchAllObj();

		if (sizeof($result) == 1) {
			return (int) $result[0]->total;
		}

		return 0;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Db\Select::getQuery()
	 */
	protected function getQuery() {
		if ($this->searchTerm !== null)  {

			// there is search term set, so we'll need to include this to where statements

			// but, there might be already some where statements in the query, so we'll create
			// new Where instance and we'll add that Where block with AND operator

			$where = Where::init();
			if ($this->searchFields !== null) {
				foreach ($this->searchFields as $field) {
					$where->orWhereLike($field, "%{$this->searchTerm}%");
				}
			} else {
				foreach ($this->getFields() as $field) {
					$where->orWhereLike($field['name'], "%{$this->searchTerm}%");
				}
			}
			$this->where($where);
		}

		return parent::getQuery();
	}

}
