<?php namespace Koldy\Db;

use Koldy\Exception;

/**
 * The SELECT query builder
 * 
 * @link http://koldy.net/docs/database/query-builder#select
 */
class Select extends Where {

	protected $fields = array();

	protected $from = array();

	protected $joins = array();

	protected $groupBy = array();

	protected $having = array();

	protected $orderBy = array();

	protected $limit = null;

	/**
	 * @param string $table [optional]
	 * @link http://koldy.net/docs/database/query-builder#select
	 */
	public function __construct($table = null) {
		if ($table !== null) {
			$this->from($table);
		}
	}
	
	/**
	 * Set the table FROM which fields will be fetched
	 * @param string $table
	 * @param string $alias
	 * @param mixed $field one field as string or more fields as array or just '*'
	 * @return \Koldy\Db\Select
	 */
	public function from($table, $alias = null, $field = null) {
		$this->from[] = array(
			'table' => $table,
			'alias' => $alias
		);

		if ($field !== null) {
			if (is_array($field)) {
				foreach ($field as $fld) {
					$this->field(($alias === null) ? $fld : "{$alias}.{$fld}");
				}
			} else {
				$this->field(($alias === null) ? $field : "{$alias}.{$field}");
			}
		}

		return $this;
	}

	/**
	 * Join two tables
	 * @param string $table
	 * @param string $firstTableField
	 * @param string $operator
	 * @param string $secondTableField
	 * @return \Koldy\Db\Select
	 * @example innerJoin('user u', 'u.id', '=', 'r.user_role_id')
	 */
	public function innerJoin($table, $firstTableField, $operator, $secondTableField) {
		$this->joins[] = array(
			'type' => 'INNER JOIN',
			'table' => $table,
			'first' => $firstTableField,
			'operator' => $operator,
			'second' => $secondTableField,
		);
		return $this;
	}

	/**
	 * Join two tables
	 * @param string $table
	 * @param string $firstTableField
	 * @param string $operator
	 * @param string $secondTableField
	 * @return \Koldy\Db\Select
	 * @example leftJoin('user u', 'u.id', '=', 'r.user_role_id')
	 */
	public function leftJoin($table, $firstTableField, $operator = null, $secondTableField = null) {
		$this->joins[] = array(
			'type' => 'LEFT JOIN',
			'table' => $table,
			'first' => $firstTableField,
			'operator' => $operator,
			'second' => $secondTableField,
		);
		return $this;
	}

	/**
	 * Join two tables
	 * @param string $table
	 * @param string $firstTableField
	 * @param string $operator
	 * @param string $secondTableField
	 * @return \Koldy\Db\Select
	 * @example rightJoin('user u', 'u.id', '=', 'r.user_role_id')
	 */
	public function rightJoin($table, $firstTableField, $operator, $secondTableField) {
		$this->joins[] = array(
			'type' => 'RIGHT JOIN',
			'table' => $table,
			'first' => $firstTableField,
			'operator' => $operator,
			'second' => $secondTableField,
		);
		return $this;
	}

	/**
	 * Join two tables
	 * @param string $table
	 * @param string $firstTableField
	 * @param string $operator
	 * @param string $secondTableField
	 * @return \Koldy\Db\Select
	 * @example join('user u', 'u.id', '=', 'r.user_role_id')
	 */
	public function join($table, $firstTableField, $operator, $secondTableField) {
		return $this->innerJoin($table, $firstTableField, $operator, $secondTableField);
	}

	/**
	 * Add one field that will be fetched
	 * @param string $field
	 * @param string $as
	 * @return \Koldy\Db\Select
	 */
	public function field($field, $as = null) {
		$this->fields[] = array(
			'name' => $field,
			'as' => $as
		);
		
		return $this;
	}

	/**
	 * Add fields to fetch by passing array of fields
	 * @param array $fields
	 * @param string $table
	 * @return \Koldy\Db\Select
	 */
	public function fields(array $fields) {
		foreach ($fields as $field => $as) {
			if (is_numeric($field)) {
				$this->field($as);
			} else {
				$this->field($field, $as);
			}
		}
		return $this;
	}

	/**
	 * Reset all fields that will be fetched
	 * @return \Koldy\Db\Select
	 */
	public function resetFields() {
		$this->fields = array();
		return $this;
	}

	/**
	 * Get the array of fields that were added to SELECT query
	 * @return array
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 * Add field to GROUP BY
	 * @param string $field
	 * @return \Koldy\Db\Select
	 */
	public function groupBy($field) {
		$this->groupBy[] = array(
			'field' => $field
		);
		return $this;
	}

	/**
	 * Reset GROUP BY (remove GROUP BY)
	 * @return \Koldy\Db\Select
	 */
	public function resetGroupBy() {
		$this->groupBy = array();
		return $this;
	}

	/**
	 * Add HAVING to your SELECT query
	 * @param string $field
	 * @param string $operator
	 * @param mixed $value
	 * @return \Koldy\Db\Select
	 */
	public function having($field, $operator = null, $value = null) {
		$this->having[] = array(
			'link' => 'AND',
			'field' => $field,
			'operator' => $operator,
			'value' => $value
		);
		return $this;
	}

	/**
	 * Add HAVING with OR operator
	 * @param string $field
	 * @param string $operator
	 * @param mixed $value
	 * @return \Koldy\Db\Select
	 */
	public function orHaving($field, $operator = null, $value = null) {
		$this->having[] = array(
			'link' => 'OR',
			'field' => $field,
			'operator' => $operator,
			'value' => $value
		);
		return $this;
	}

	/**
	 * Reset HAVING statement
	 * @return \Koldy\Db\Select
	 */
	public function resetHaving() {
		$this->having = array();
		return $this;
	}

	/**
	 * Add field to ORDER BY
	 * @param string $field
	 * @param string $direction
	 * @return \Koldy\Db\Select
	 */
	public function orderBy($field, $direction = null) {
		if ($direction === null) {
			$direction = 'ASC';
		} else {
			$direction = strtoupper($direction);
		}
		
		if ($direction !== 'ASC' && $direction !== 'DESC') {
			throw new Exception("Can not use invalid direction order ({$direction}) in ORDER BY statement");
		}
		
		$this->orderBy[] = array(
			'field' => $field,
			'direction' => $direction
		);
		return $this;
	}

	/**
	 * Reset ORDER BY (remove ORDER BY)
	 * @return \Koldy\Db\Select
	 */
	public function resetOrderBy() {
		$this->orderBy = array();
		return $this;
	}

	/**
	 * Set the LIMIT on query results
	 * @param int $start
	 * @param int $howMuch
	 * @return \Koldy\Db\Select
	 */
	public function limit($start, $howMuch) {
		$this->limit = new \stdClass;
		$this->limit->start = $start;
		$this->limit->howMuch = $howMuch;
		return $this;
	}

	/**
	 * Limit the results by "page"
	 * @param int $number
	 * @param int $limitPerPage
	 * @return \Koldy\Db\Select
	 */
	public function page($number, $limitPerPage) {
		return $this->limit(($number -1) * $limitPerPage, $limitPerPage);
	}

	/**
	 * Reset LIMIT (remove the LIMIT)
	 * @return \Koldy\Db\Select
	 */
	public function resetLimit() {
		$this->limit = null;
		return $this;
	}

	/**
	 * Get the query string prepared for PDO
	 * @return string
	 */
	protected function getQuery() {
		if (sizeof($this->from) == 0) {
			throw new Exception('Can not build SELECT query, there is no FROM table defined');
		}

		$this->bindings = array();
		$query = "SELECT\n";

		if (sizeof($this->fields) == 0) {
			$query .= "\t*";
		} else {
			foreach ($this->fields as $fld) {
				$field = $fld['name'];
				$as = $fld['as'];
				
				$query .= "\t{$field}";
				if ($as !== null) {
					$query .= " as {$as}";
				}
				
				$query .= ",\n";
			}

			$query = substr($query, 0, -2);
		}

		$query .= "\nFROM";
		foreach ($this->from as $from) {
			if ($from['table'] instanceof static) {
				/* @var $subSelect \Koldy\Db\Select */
				$subSelect = $from['table'];
				$subSql = $subSelect->__toString();
				$subSql = str_replace("\n", "\n\t", $subSql);
				$query .= " (\n\t{$subSql}\n) {$from['alias']}\n";

				$subSelectBindings = $subSelect->getBindings();
				if (count($subSelectBindings) > 0) {
					$this->bindings += $subSelectBindings;
				}
			} else {
				$query .= "\n\t{$from['table']}";
				if ($from['alias'] !== null) {
					$query .= " as {$from['alias']},";
				} else {
					$query .= ',';
				}
			}
		}
		$query = substr($query, 0, -1);

		foreach ($this->joins as $join) {
			$query .= "\n\t{$join['type']} {$join['table']} ON ";
			
			if (is_array($join['first'])) {
				foreach ($join['first'] as $joinArg) {
					if ($joinArg instanceof Expr) {
						$query .= "{$joinArg} AND ";

					} else if (is_array($joinArg) && count($joinArg) == 2) {
						$query .= "{$joinArg[0]} = {$joinArg[1]} AND ";

					} else if (is_array($joinArg) && count($joinArg) == 3) {
						$query .= "{$joinArg[0]} {$joinArg[1]} {$joinArg[2]} AND ";

					} else if (is_array($joinArg) && count($joinArg) == 4) {
						if (substr($query, -5) == ' AND ') {
							$query = substr($query, 0, -5);
						}

						$query .= " {$joinArg[0]} {$joinArg[1]} {$joinArg[2]}  {$joinArg[3]} AND ";

					} else {
						throw new Exception('Unknown JOIN argument');

					}
				}

				$query = substr($query, 0, -5);
			} else {
				if ($join['operator'] === null) {
					throw new Exception('Operator can\'t be null');
				}

				if ($join['second'] === null) {
					throw new Exception('Second parameter can\'t be null');
				}

				$query .= "{$join['first']} {$join['operator']} {$join['second']}";
			}
		}

		if ($this->hasWhere()) {
			$query .= "\nWHERE\n\t" . trim($this->getWhereSql());
		}

		if (sizeof($this->groupBy) > 0) {
			$query .= "\nGROUP BY";
			foreach ($this->groupBy as $r) {
				$query .= " {$r['field']},";
			}
			$query = substr($query, 0, -1);
		}

		$sizeofHaving = count($this->having);
		if ($sizeofHaving > 0) {
			$query .= "\nHAVING";
			if ($sizeofHaving == 1) {
				$nl = ' ';
			} else {
				$nl = "\n\t";
			}
			
			foreach ($this->having as $index => $having) {
				$link = ($index > 0) ? "{$having['link']} " : '';
				if ($having['value'] instanceof Expr) {
					$query .= "{$nl}{$link}{$having['field']} {$having['operator']} {$having['value']}";
				} else {
					$query .= "{$nl}{$link}{$having['field']} {$having['operator']} :having{$index}";
					$this->bindings['having' . $index] = $having['value'];
				}
			}
		}

		if (sizeof($this->orderBy) > 0) {
			$query .= "\nORDER BY";
			foreach ($this->orderBy as $r) {
				$query .= "\n\t{$r['field']} {$r['direction']},";
			}
			$query = substr($query, 0, -1);
		}

		if ($this->limit !== null) {
			$query .= "\nLIMIT {$this->limit->start}, {$this->limit->howMuch}";
		}

		return $query;
	}

	/**
	 * Fetch all records by this query
	 * @param const $fetchMode [optional] default PDO::FETCH_ASSOC
	 * @return array
	 */
	public function fetchAll($fetchMode = \PDO::FETCH_ASSOC) {
		return $this->getAdapter()->query($this->getQuery(), $this->bindings, $fetchMode);
	}

	/**
	 * Fetch all records as array of objects
	 * @return array
	 */
	public function fetchAllObj() {
		return $this->fetchAll(\PDO::FETCH_OBJ);
	}

	/**
	 * Fetch only first record as object or return false if there is no records
	 * @param const $fetchMode [optional] default PDO::FETCH_ASSOC
	 * @return \stdClass|bool false if database didn't return anything
	 */
	public function fetchFirst($fetchMode = \PDO::FETCH_ASSOC) {
		$this->resetLimit()->limit(0, 1);
		$results = $this->fetchAll($fetchMode);
		return isset($results[0]) ? $results[0] : false;
	}

	/**
	 * Fetch only first record as object
	 * @return stdClass|bool false if database didn't return anything
	 */
	public function fetchFirstObj() {
		return $this->fetchFirst(\PDO::FETCH_OBJ);
	}

}
