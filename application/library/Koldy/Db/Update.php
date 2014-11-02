<?php namespace Koldy\Db;

use Koldy\Exception;

/**
 * The UPDATE query builder.
 * @author vkoudela
 * @link http://koldy.net/docs/database/query-builder#update
 */
class Update extends Where {

	/**
	 * The table name on which UPDATE will be performed
	 * @var string
	 */
	protected $table = null;

	/**
	 * The key-value pairs of fields and values to be set
	 * @var array
	 */
	protected $what = array();

	/**
	 * @param string $table
	 * @param array $values [optional] auto set values in this query
	 * @link http://koldy.net/docs/database/query-builder#update
	 */
	public function __construct($table, array $values = null) {
		$this->table = $table;
		
		if ($values !== null) {
			$this->setValues($values);
		}
	}
	
	/**
	 * Set field to be updated
	 * @param string $field
	 * @param mixed $value
	 * @return \Koldy\Db\Update
	 */
	public function set($field, $value) {
		$this->what[$field] = $value;
		return $this;
	}
	
	/**
	 * Set the values to be updated
	 * @param array $values
	 * @return \Koldy\Db\Update
	 */
	public function setValues(array $values) {
		$this->what = $values;
		return $this;
	}
	
	/**
	 * Increment numeric field's value in database
	 * @param string $field
	 * @param number $howMuch
	 * @return \Koldy\Db\Update
	 */
	public function increment($field, $howMuch = 1) {
		return $this->set($field, new Expr("{$field} + {$howMuch}"));
	}
	
	/**
	 * Decrement numeric field's value in database
	 * @param string $field
	 * @param number $howMuch
	 * @return \Koldy\Db\Update
	 */
	public function decrement($field, $howMuch = 1) {
		return $this->set($field, new Expr("{$field} - {$howMuch}"));
	}
	
	/**
	 * Get the query
	 */
	protected function getQuery() {
		$this->bindings = array();
		$sql = "UPDATE {$this->table}\nSET\n";
		
		if (sizeof($this->what) == 0) {
			throw new Exception('Can not build UPDATE query, SET is not defined');
		}
		
		foreach ($this->what as $field => $value) {
			$sql .= "\t{$field} = ";
			if ($value instanceof Expr) {
				$sql .= "{$value},\n";
			} else {
				$key = $field . (static::getKeyIndex());
				$sql .= ":{$key},\n";
				$this->bindings[$key] = $value;
			}
		}
		
		$sql = substr($sql, 0, -2);
		
		if ($this->hasWhere()) {
			$sql .= "\nWHERE{$this->getWhereSql()}";
		}
		
		return $sql;
	}
	
}
