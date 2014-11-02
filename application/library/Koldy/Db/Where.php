<?php namespace Koldy\Db;

use Koldy\Exception;

class Where extends Query {


	/**
	 * The array of where statements
	 * @var array
	 */
	private $where = array();


	/**
	 * Initialize instance of this class
	 * @return \Koldy\Db\Where
	 */
	public static function init() {
		return new static();
	}


	/**
	 * Add condition to where statements
	 * 
	 * @param string $link
	 * @param string $field
	 * @param mixed $value
	 * @param string $operator
	 * @return \Koldy\Db\Where
	 */
	private function addCondition($link, $field, $value, $operator) {
		$this->where[] = array(
			'link' => $link,
			'field' => $field,
			'operator' => $operator,
			'value' => $value
		);

		return $this;
	}


	/**
	 * Add AND where statement
	 * 
	 * @param string $field
	 * @param mixed $valueOrOperator
	 * @param mixed $value
	 * @return \Koldy\Db\Where
	 * 
	 * @example where('id', 2) produces WHERE id = 2
	 * @example where('id', '00385') produces WHERE id = '00385'
	 * @example where('id', '>', 5) produces WHERE id > 5
	 * @example where('id', '<=', '0100') produces WHERE id <= '0100'
	 */
	public function where($field, $valueOrOperator = null, $value = null) {
		if (is_string($field) && $valueOrOperator === null) {
			throw new \InvalidArgumentException('Invalid second argument; argument must not be null');
		}

		return $this->addCondition('AND', $field, ($value === null) ? $valueOrOperator : $value, ($value === null) ? '=' : $valueOrOperator);
	}


	/**
	 * Add OR where statement
	 * 
	 * @param string $field
	 * @param mixed $valueOrOperator
	 * @param mixed $value
	 * @return \Koldy\Db\Where
	 */
	public function orWhere($field, $valueOrOperator = null, $value = null) {
		if (is_string($field) && $valueOrOperator === null) {
			throw new \InvalidArgumentException('Invalid second argument; argument must not be null');
		}

		return $this->addCondition('OR', $field, ($value === null) ? $valueOrOperator : $value, ($value === null) ? '=' : $valueOrOperator);
	}


	/**
	 * Add WHERE field IS NULL
	 * 
	 * @param string $field
	 * @return \Koldy\Db\Where
	 */
	public function whereNull($field) {
		return $this->addCondition('AND', $field, new Expr('NULL'), 'IS');
	}


	/**
	 * Add OR field IS NULL
	 * 
	 * @param string $field
	 * @return \Koldy\Db\Where
	 */
	public function orWhereNull($field) {
		return $this->addCondition('OR', $field, new Expr('NULL'), 'IS');
	}


	/**
	 * Add WHERE field IS NOT NULL
	 * 
	 * @param string $field
	 * @return \Koldy\Db\Where
	 */
	public function whereNotNull($field) {
		return $this->addCondition('AND', $field, new Expr('NULL'), 'IS NOT');
	}


	/**
	 * Add OR field IS NOT NULL
	 * 
	 * @param string $field
	 * @return \Koldy\Db\Where
	 */
	public function orWhereNotNull($field) {
		return $this->addCondition('OR', $field, new Expr('NULL'), 'IS NOT');
	}


	/**
	 * Add WHERE field is BETWEEN two values
	 * 
	 * @param string $field
	 * @param mixed $value1
	 * @param mixed $value2
	 * @return \Koldy\Db\Where
	 */
	public function whereBetween($field, $value1, $value2) {
		return $this->addCondition('AND', $field, array($value1, $value2), 'BETWEEN');
	}


	/**
	 * Add OR field is BETWEEN two values
	 * 
	 * @param string $field
	 * @param mixed $value1
	 * @param mixed $value2
	 * @return \Koldy\Db\Where
	 */
	public function orWhereBetween($field, $value1, $value2) {
		return $this->addCondition('OR', $field, array($value1, $value2), 'BETWEEN');
	}


	/**
	 * Add WHERE field is NOT BETWEEN two values
	 * 
	 * @param string $field
	 * @param mixed $value1
	 * @param mixed $value2
	 * @return \Koldy\Db\Where
	 */
	public function whereNotBetween($field, $value1, $value2) {
		return $this->addCondition('AND', $field, array($value1, $value2), 'NOT BETWEEN');
	}


	/**
	 * Add OR field is NOT BETWEEN two values
	 * 
	 * @param string $field
	 * @param mixed $value1
	 * @param mixed $value2
	 * @return \Koldy\Db\Where
	 */
	public function orWhereNotBetween($field, $value1, $value2) {
		return $this->addCondition('OR', $field, array($value1, $value2), 'NOT BETWEEN');
	}


	/**
	 * Add WHERE field is IN array of values
	 * 
	 * @param string $field
	 * @param array $values
	 * @return \Koldy\Db\Where
	 */
	public function whereIn($field, array $values) {
		return $this->addCondition('AND', $field, array_values($values), 'IN');
	}


	/**
	 * Add OR field is IN array of values
	 * 
	 * @param string $field
	 * @param array $values
	 * @return \Koldy\Db\Where
	 */
	public function orWhereIn($field, array $values) {
		// todo: extend this with select query
		return $this->addCondition('OR', $field, array_values($values), 'IN');
	}


	/**
	 * Add WHERE field is NOT IN array of values
	 * 
	 * @param string $field
	 * @param array $values
	 * @return \Koldy\Db\Where
	 */
	public function whereNotIn($field, array $values) {
		return $this->addCondition('AND', $field, array_values($values), 'NOT IN');
	}


	/**
	 * Add OR field is NOT IN array of values
	 * 
	 * @param string $field
	 * @param array $values
	 * @return \Koldy\Db\Where
	 */
	public function orWhereNotIn($field, array $values) {
		return $this->addCondition('OR', $field, array_values($values), 'NOT IN');
	}


	/**
	 * Add WHERE field is LIKE
	 * 
	 * @param string $field
	 * @param string $value
	 * @return \Koldy\Db\Where
	 */
	public function whereLike($field, $value) {
		return $this->addCondition('AND', $field, $value, 'LIKE');
	}


	/**
	 * Add OR field is LIKE
	 * 
	 * @param string $field
	 * @param string $value
	 * @return \Koldy\Db\Where
	 */
	public function orWhereLike($field, $value) {
		return $this->addCondition('OR', $field, $value, 'LIKE');
	}


	/**
	 * Add WHERE field is NOT LIKE
	 * 
	 * @param string $field
	 * @param string $value
	 * @return \Koldy\Db\Where
	 */
	public function whereNotLike($field, $value) {
		return $this->addCondition('AND', $field, $value, 'NOT LIKE');
	}


	/**
	 * Add OR field is NOT LIKE
	 * 
	 * @param string $field
	 * @param string $value
	 * @return \Koldy\Db\Where
	 */
	public function orWhereNotLike($field, $value) {
		return $this->addCondition('OR', $field, $value, 'NOT LIKE');
	}


	/**
	 * Is there any WHERE statement
	 * 
	 * @return boolean
	 */
	protected function hasWhere() {
		return count($this->where) > 0;
	}


	/**
	 * Get where statement appended to query
	 * 
	 * @param array $whereArray
	 * @param int $cnt
	 * @return string
	 */
	protected function getWhereSql(array $whereArray = null, $cnt = 0) {
		$query = '';

		if ($whereArray === null) {
			$whereArray = $this->where;
		}

		foreach ($whereArray as $index => $where) {
			if ($index > 0) {
				$query .= "\t{$where['link']}";
			}

			$field = $where['field'];
			$value = $where['value'];

			if (gettype($field) == 'object' && $value === null) {
				// function or instance of self is passed, do something

				$q = ($field instanceof self) ? $field : $field(new static());
				if ($q === null) {
					throw new Exception('Can not build query, statement\'s where function didn\'t return anything');
				}

				$whereSql = trim($q->getWhereSql(null, $cnt++));
				$whereSql = str_replace("\n", ' ', $whereSql);
				$whereSql = str_replace("\t", '', $whereSql);

				$query .= " ({$whereSql})\n";
				foreach ($q->getBindings() as $k => $v) {
					$this->bindings[$k] = $v;
				}

			} else if ($value instanceof Expr) {
				$query .= " ({$field} {$where['operator']} {$value})\n";

			} else if (is_array($value)) {

				switch($where['operator']) {
					case 'BETWEEN':
					case 'NOT BETWEEN':
						$query .= " ({$field} {$where['operator']} ";

						if ($value[0] instanceof Expr) {
							$query .= $value[0];
						} else {
							$key = 'f' . str_replace('.', '_', $field) . (static::getKeyIndex());
							$query .= ":{$key}";
							$this->bindings[$key] = $value[0];
						}

						$query .= ' AND ';

						if ($value[1] instanceof Expr) {
							$query .= $value[1];
						} else {
							$key = 'f' . str_replace('.', '_', $field) . (static::getKeyIndex());
							$query .= ":{$key}";
							$this->bindings[$key] = $value[1];
						}

						$query .= ")\n";
						break;

					case 'IN':
					case 'NOT IN':
						$query .= " ({$field} {$where['operator']} (";

						foreach ($value as $val) {
							$key = 'f' . str_replace('.', '_', $field) . (static::getKeyIndex());
							$query .= ":{$key},";
							$this->bindings[$key] = $val;
						}

						$query = substr($query, 0, -1);
						$query .= "))\n";
						break;

					// default: nothing by default
				}

			} else {
				$key = 'f' . str_replace('.', '_', $field) . (static::getKeyIndex());
				$query .= " ({$field} {$where['operator']} :{$key})\n";
				$this->bindings[$key] = $where['value'];

			}
			
		}

		return $query;
	}


	/**
	 * This method is here because this class sometimes has to be initialized.
	 * Otherwise, this method must be overwritten in child class!
	 */
	protected function getQuery() {
		return null;
	}

}
