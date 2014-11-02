<?php namespace Koldy\Db;

use Koldy\Db;
use Koldy\Exception;

/**
 * Use this class if you want to insert multiple rows at once
 * @link http://koldy.net/docs/database/query-builder#insert
 * 
 */
class Insert extends Query {


	/**
	 * The table on which insert will be executed
	 * 
	 * @var string
	 */
	protected $table = null;


	/**
	 * The fields on which will data be inserted
	 * 
	 * @var array
	 */
	protected $fields = array();


	/**
	 * Array of rows of data
	 * 
	 * @var array
	 */
	protected $data = array();


	/**
	 * The INSERT INTO () SELECT feature
	 * 
	 * @var Query|string
	 */
	protected $select = null;


	/**
	 * Construct the object
	 * 
	 * @param string $table
	 * @param array $rowValues is key => value array to insert into database
	 * @link http://koldy.net/docs/database/query-builder#insert
	 */
	public function __construct($table, array $rowValues = null) {
		$this->table = $table;

		if ($rowValues != null) {
			if (isset($rowValues[0]) && is_array($rowValues[0])) {
				$this->addRows($rowValues);
			} else {
				foreach ($rowValues as $field => $value) {
					$this->field($field);
				}
				$this->add($rowValues);
			}
		}
	}


	/**
	 * The table on which insert will be executed
	 * 
	 * @param string $table
	 * @return \Koldy\Db\Insert
	 */
	public function table($table) {
		$this->table = $table;
		return $this;
	}


	/**
	 * Set one field on which query will be executed
	 * 
	 * @param string $field
	 * @return \Koldy\Db\Insert
	 */
	public function field($field) {
		$this->fields[] = $field;
		return $this;
	}


	/**
	 * Set the fields that will be inserted
	 * 
	 * @param array $fields
	 * @return \Koldy\Db\Insert
	 */
	public function fields(array $fields) {
		$this->fields = array_values($fields);
		return $this;
	}


	/**
	 * Add row
	 * 
	 * @param array $data array of values in row
	 * @return \Koldy\Db\Insert
	 */
	public function add(array $data) {
		if (!isset($data[0]) && count($this->fields) == 0) {
			$this->fields(array_keys($data));
		}

		$this->data[] = $data;
		return $this;
	}


	/**
	 * Add multiple rows into insert
	 * 
	 * @param array $rows
	 * @return \Koldy\Db\Insert
	 */
	public function addRows(array $rows) {
		foreach ($rows as $index => $row) {
			$this->add($row);
		}
		return $this;
	}


	/**
	 * The select query to insert from
	 * 
	 * @param Query|string $selectQuery
	 * @return \Koldy\Db\Insert
	 */
	public function selectFrom($selectQuery) {
		$this->select = $selectQuery;
		return $this;
	}


	/**
	 * Get the Query string
	 * 
	 * @throws Exception
	 * @return string
	 */
	protected function getQuery() {
		$this->bindings = array();

		if (sizeof($this->data) == 0 && $this->select === null) {
			throw new Exception('Can not execute Insert query, no records to insert');
		}

		$hasFields = count($this->fields) > 0;

		$query = "INSERT INTO {$this->table}";

		if ($hasFields) {
			$query .= ' (';

			foreach ($this->fields as $field) {
				$query .= $field . ',';
			}

			$query = substr($query, 0, -1) . ')';
		}

		if ($this->select !== null) {
			$query .= "\n(\n\t" . str_replace("\n", "\n\t", $this->select->__toString()) . "\n)";
			$this->bindings = $this->select->getBindings();
		} else {
			$query .= "\nVALUES\n";
			foreach ($this->data as $index => $row) {
				$query .= "\t(";

				if ($hasFields) {

					foreach ($this->fields as $field) {
						if (isset($row[$field])) {
							$val = $row[$field];

							if ($val instanceof Expr) {
								$query .= "{$val},";
							} else {
								$key = 'field' . $index . (static::getKeyIndex());
								$query .= ":{$key},";
								$this->bindings[$key] = $val;
							}
						} else {
							$query .= 'NULL,';
						}
					}

					$query = substr($query, 0, -1);

				} else {
					$values = array_values($row);

					if ($index == 0) {
						$targetCount = count($row);
					} else if (sizeof($row) != $targetCount) {
						throw new Exception('Can not build INSERT query, column count is not the same in all data records');
					}

					foreach ($values as $i => $val) {
						if ($val instanceof Expr) {
							$query .= "{$val},";
						} else {
							$key = 'field' . $i . (static::getKeyIndex());
							$query .= ":{$key},";
							$this->bindings[$key] = $val;
						}
					}

					$query = substr($query, 0, -1);
				}

				$query .= "),\n";
			}

			$query = substr($query, 0, -2);
		}

		return $query;
	}

}
