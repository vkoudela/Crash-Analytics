<?php namespace Koldy\Db;

use Koldy\Exception;

/**
 * The DELETE query builder
 * 
 * @link http://koldy.net/docs/database/query-builder#delete
 */
class Delete extends Where {


	/**
	 * The table name on which DELETE will be performed
	 * @var string
	 */
	protected $table = null;


	/**
	 * @param string $table
	 * @link http://koldy.net/docs/database/query-builder#delete
	 */
	public function __construct($table) {
		$this->table = $table;
	}


	/**
	 * Get the query
	 */
	protected function getQuery() {
		$this->bindings = array();
		$sql = "DELETE FROM {$this->table}";
		
		if ($this->hasWhere()) {
			$sql .= "\nWHERE{$this->getWhereSql()}";
		}
		
		return $sql;
	}

}
