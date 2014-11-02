<?php namespace Koldy\Db;

use Koldy\Db;
use Koldy\Exception;
use Koldy\Log;

/**
 * Model is abstract class that needs to be extended with your defined class.
 * When you extend it, framework will know with which table it needs to work; or
 * simply define the table name you need. Check out the docs in link for more examples.
 * 
 * @link http://koldy.net/docs/database/models
 */
abstract class Model {


	/**
	 * The connection string on which the queries will be executed. The
	 * connection string must be previously defined as connection in
	 * config.database.php.
	 * 
	 * @var string
	 */
	protected static $connection = null;


	/**
	 * If you don't define the table name, the framework will assume the table
	 * name by the called class name.
	 * 
	 * @var string
	 */
	protected static $table = null;


	/**
	 * While working with tables in database, framework will always assume that
	 * you have the field named "id" as unique identifier. If you have
	 * your primary key with different name, please define it in the child
	 * class.
	 * 
	 * @var string
	 */
	protected static $primaryKey = 'id';


	/**
	 * Assume that this table has auto increment field and that field is primary field.
	 * @var bool
	 */
	protected static $autoIncrement = true;
	
	/**
	 * The array of fields that will naver be injected into query when calling
	 * the save() method. Be aware that this doesn't affect static update()
	 * method.
	 * 
	 * @var array
	 */
	protected static $neverUpdate = array();


	/**
	 * The data holder in this object
	 * 
	 * @var array
	 */
	protected $data = null;


	/**
	 * This is the array that holds informations loaded from database. When
	 * you call save() method, this data will be compared to the data set in
	 * object and update method will set only fields that are changed. If there
	 * is no change, update() method will return 0 without triggering query on
	 * database.
	 * 
	 * @var array
	 */
	protected $originalData = null;


	/**
	 * Construct the instance with or without starting data
	 * 
	 * @param array $data
	 */
	public function __construct(array $data = null) {
		if ($data !== null) {
			$setOriginalData = false;

			foreach ($data as $key => $value) {
				$this->$key = $value;
				if (!is_array(static::$primaryKey) && $key === static::$primaryKey) {
					$setOriginalData = true;
				}
			}

			if ($setOriginalData) {
				$this->originalData = $data;
			}
		}
	}


	public function __get($property) {
		return (isset($this->data[$property]))
			? $this->data[$property]
			: null;
	}


	public function __set($property, $value) {
		$this->data[$property] = $value;
	}


	/**
	 * Set the array of values
	 * 
	 * @param array $values
	 * @return \Koldy\Db\Model
	 */
	public function set(array $values) {
		foreach ($values as $key => $value) {
			$this->data[$key] = $value;
		}

		return $this;
	}


	/**
	 * Gets all data that this object currently has
	 * 
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}


	/**
	 * Does this object has a field?
	 * 
	 * @param string $field
	 * @return bool
	 */
	public function has($field) {
		return array_key_exists($field, $this->data);
	}


	/**
	 * Get the adapter for this model
	 * 
	 * @return  \Koldy\Db\Adapter
	 */
	public static function getAdapter() {
		return \Koldy\Db::getAdapter(static::$connection);
	}


	/**
	 * Get the connection string defined in this model
	 * 
	 * @return string|null
	 */
	public static function getConnection() {
		return static::$connection;
	}


	/**
	 * Get the table name for database for this model. If your model class is
	 * User\Login\History, then the database table name will be user_login_history
	 * 
	 * @return string
	 */
	public static function getTableName() {
		if (static::$table === null) {
			return str_replace('\\', '_', strtolower(get_called_class()));
		}

		return static::$table;
	}


	/**
	 * Insert the record in database with given array of data
	 * @param mixed $data pass array or valid instance of \Koldy\Db\Model
	 * 
	 * @return \Koldy\Db\Model|false False if insert failed, otherwise, instance of this model
	 * @throws \Koldy\Exception
	 */
	public static function create($data) {
		if ($data instanceof Model) {
			$data = $data->getData();
		}

		$insert = new Insert(static::getTableName(), $data);
		$ok = $insert->exec(static::$connection);

		if (static::$autoIncrement) {
			$data[static::$primaryKey] = static::getLastInsertId();
		}

		return new static($data);
	}


	/**
	 * If you statically created new record in database to the table with auto
	 * incrementing field, then you can use this static method to get the
	 * generated primary key
	 * 
	 * @return integer
	 * @example
	 * 
	 * 		if (User::create(array('first_name' => 'John', 'last_name' => 'Doe'))) {
	 *   		echo User::getLastInsertId();
	 *   	}
	 */
	public static function getLastInsertId() {
		if (static::$autoIncrement) {
			return static::getAdapter(static::$connection)->getLastInsertId();
		} else {
			Log::warning('Can not get last insert ID when model ' . get_called_class() . ' doesn\'t have auto_increment field');
			return null;
		}
	}


	/**
	 * Update the table with given array of data. Be aware that if you don't
	 * pass the second parameter, then the whole table will be updated (the
	 * query will be executed without the WHERE statement).
	 * 
	 * @param  array $data
	 * @param  mixed $onWhat OPTIONAL if you pass single value, framework will
	 * assume that you passed primary key value. If you pass assoc array,
	 * then the framework will use those to create the WHERE statement.
	 * 
	 * @example
	 * 
	 * 		User::update(array('first_name' => 'new name'), 5) will execute:
	 *   	UPDATE user SET first_name = 'new name' WHERE id = 5
	 * 
	 * 		User::update(array('first_name' => 'new name'), array('disabled' => 0)) will execute:
	 *   	UPDATE user SET first_name = 'new name' WHERE disabled = 0
	 *  
	 * @return int number of affected rows
	 */
	public static function update(array $data, $where = null) {
		$update = new Update(static::getTableName(), $data);

		if ($where !== null) {
			if ($where instanceof Where) {
				$update->where($where);
			} else if (is_array($where)) {
				foreach ($where as $field => $value) {
					$update->where($field, $value);
				}
			} else if (!is_array(static::$primaryKey) && (is_numeric($where) || is_string($where))) {
				$update->where(static::$primaryKey, $where);
			}
		}

		return $update->exec(static::$connection);
	}


	/**
	 * Save this initialized object into database.
	 * 
	 * @return integer how many rows is affected
	 */
	public function save() {
		$data = $this->getData();
		$originalData = $this->originalData;
		$toUpdate = array();

		foreach ($data as $field => $value) {
			if (isset($originalData[$field]) || $originalData[$field] === null) {
				if ($value !== $originalData[$field]) {
					$toUpdate[$field] = $value;
				}
			} else {
				$toUpdate[$field] = $value;
			}
		}

		if (sizeof($toUpdate) > 0) {
			if (!is_array(static::$primaryKey)) {
				if (isset($originalData[static::$primaryKey])) {
					// we have pk value, so lets update
					$update = new Update(static::getTableName());
					$update->setValues($toUpdate);

					if (!is_array(static::$primaryKey)) {
						$update->where(static::$primaryKey, $data[static::$primaryKey]);
					} else {
						foreach (static::$primaryKey as $field) {
							$update->where($field, $data[$field]);
						}
					}

					try {
						$result = $update->exec(static::$connection);
					} catch (Exception $e) {
						$result = false;
					}

					if ($result !== false) {
						$this->originalData = $this->data;
					}

					return $result;
				} else {
					// we don't have pk value, so lets insert
					$insert = new Insert(static::getTableName());
					$insert->add($toUpdate);

					try {
						$insert->exec(static::$connection);
					} catch (Exception $e) {
						return false;
					}

					$this->data[static::$primaryKey] = Db::getAdapter(static::$connection)->getLastInsertId();
					$this->originalData = $this->data;
				}
			} else {
				// TODO: Implementirati multiple key
				throw new Exception('Multiple primary key not implemented');
			}

		}

		return true;
	}


	/**
	 * Increment one numeric field in table on the row identified by primary key.
	 * You can use this only if your primary key is just one field.
	 * 
	 * @param string $field
	 * @param mixed $where the primary key value of the record
	 * @param int $howMuch [optional] default 1
	 * @return int number of affected rows
	 */
	public static function increment($field, $where, $howMuch = 1) {
		$update = Db::update(static::getTableName())->increment($field, $howMuch);

		if ($where instanceof Where) {
			$update->where($where);
		} else if (is_array($where)) {
			foreach ($where as $field => $value) {
				$update->where($field, $value);
			}
		} else if (!is_array(static::$primaryKey) && (is_numeric($where) || is_string($where))) {
			$update->where(static::$primaryKey, $where);
		} else {
			throw new Exception('Unhandeled increment case in DB model');
		}

		return $update->exec(static::$connection);
	}


	/**
	 * Delete one or more records from the table defined in this model. If you
	 * pass array, then array must contain field names and values that will be
	 * used in WHERE statement. If you pass primitive value, method will treat
	 * that as passed value for primary key field.
	 * 
	 * @param mixed $where
	 * @return integer How many records is deleted
	 * @example User::delete(1);
	 * @example User::delete(array('group_id' => 5, 'parent_id' => 10));
	 * @example User::delete(array('parent_id' => 10, array('time', '>', '2013-08-01 00:00:00')))
	 * @return int number of affected rows
	 * @link http://koldy.net/docs/database/models#delete
	 */
	public static function delete($where) {
		$delete = new Delete(static::getTableName());

		if ($where instanceof Where) {
			$delete->where($where);
		} else if (is_array($where)) {
			foreach ($where as $field => $value) {
				$delete->where($field, $value);
			}
		} else if (!is_array(static::$primaryKey) && (is_numeric($where) || is_string($where))) {
			$delete->where(static::$primaryKey, $where);
		}

		return $delete->exec(static::$connection);
	}


	/**
	 * The same as static::delete(), only this will work if object is populated with data
	 * 
	 * @see \Koldy\Db\Model::delete()
	 * @return boolean|int False if query failes; number of affected rows if query passed
	 */
	public function destroy() {
		$pk = static::$primaryKey;

		if (is_array($pk)) {
			$where = array();
			$data = $this->getData();
			foreach ($pk as $field) {
				if (!isset($data[$field])) {
					throw new Exception("Can not destroy row from database when object doesn't contain '{$field}' in loaded data");
				}
				$where[$field] = $data[$field];
			}

			return static::delete($where);
		} else if (!isset($this->data[$pk])) {
			throw new Exception('Can not destroy row from database when object doesn\'t contain primary key\'s value');
		} else {
			return static::delete($this->data[$pk]);
		}
	}


	/**
	 * Fetch one record from database. You can pass one or two parameters.
	 * If you pass only one parameter, framework will assume that you want to
	 * fetch the record from database according to primary key defined in
	 * model. Otherwise, you can fetch the record by any other field you have.
	 * If your criteria returnes more then one records, only first record will
	 * be taken.
	 * 
	 * @param  mixed $field primaryKey value, single field or assoc array of arguments for query
	 * @param  mixed $value [optional]
	 * @param array $fields [optional]
	 * @return  new static|false False will be returned if record is not found
	 * @link http://koldy.net/docs/database/models#fetchOne
	 */
	public static function fetchOne($field, $value = null, array $fields = null) {
		$select = static::query();

		if ($fields !== null) {
			$select->fields($fields);
		}

		if ($value === null) {
			if (is_array($field)) {

				foreach ($field as $k => $v) {
					$select->where($k, $v);
				}

			} else if (is_array(static::$primaryKey)) {
				throw new Exception('Can not build SELECT query when primary key is not single column');

			} else if ($field instanceof Where) {
				$select->where($field);

			} else {
				$select->where(static::$primaryKey, $field);

			}

		} else {
			$select->where($field, $value);

		}

		$record = $select->fetchFirst();
		if ($record === false) {
			return false;
		} else {
			return new static($record);
		}
	}


	/**
	 * Fetch the array of records from database
	 * 
	 * @param mixed $where the WHERE condition
	 * @param array $fields [optional] array of fields to select; by default, all fields will be fetched
	 * @return array array of initialized objects of the model this method is called on
	 * @link http://koldy.net/docs/database/models#fetch
	 */
	public static function fetch($where, array $fields = null) {
		$select = static::query();

		if ($fields !== null) {
			$select->fields($fields);
		}

		if ($where instanceof Where) {
			$select->where($where);
		} else if (is_array($where)) {
			foreach ($where as $field => $value) {
				$select->where($field, $value);
			}
		} else if (!is_array(static::$primaryKey) && (is_numeric($where) || is_string($where))) {
			$select->where(static::$primaryKey, $where);
		}

		$records = $select->fetchAll();
		$data = array();

		foreach ($records as $r) {
			$data[] = new static($r);
		}

		return $data;
	}


	/**
	 * Fetch all records from database
	 * 
	 * @param const $fetchMode [optional] default PDO::FETCH_ASSOC
	 * @return array
	 * @link http://www.php.net/manual/en/pdo.constants.php
	 */
	public static function all($fetchMode = \PDO::FETCH_ASSOC) {
		$select = static::query();
		return $select->fetchAll($fetchMode);
	}


	/**
	 * Fetch key value pairs from database table
	 * 
	 * @param string $keyField
	 * @param string $valueField
	 * @param mixed $where [optional]
	 * @param string $orderField [optional]
	 * @param string $orderDirection [optional]
	 * @return array or empty array if not found
	 * @link http://koldy.net/docs/database/models#fetchKeyValue
	 */
	public static function fetchKeyValue($keyField, $valueField, $where = null, $orderField = null, $orderDirection = null) {
		$select = static::query()
			->field($keyField, 'key_field')
			->field($valueField, 'value_field');

		if ($where !== null) {
			if ($where instanceof Where) {
				$select->where($where);
			} else if (is_array($where)) {
				foreach ($where as $field => $value) {
					$select->where($field, $value);
				}
			} else if (!is_array(static::$primaryKey) && (is_numeric($where) || is_string($where))) {
				$select->where(static::$primaryKey, $where);
			}
		}

		if ($orderField !== null) {
			$select->orderBy($orderField, $orderDirection);
		}

		$records = $select->fetchAllObj();
		$data = array();

		foreach ($records as $r) {
			$data[$r->key_field] = $r->value_field;
		}

		return $data;
	}


	/**
	 * Fetch numeric array of values from one column in database
	 * 
	 * @param string $field
	 * @param mixed $where [optional]
	 * @param string $orderField [optional]
	 * @param string $orderDirection [optional]
	 * @param integer $limit [optional]
	 * @return array or empty array if not found
	 * @example User::fetchArrayOf('id', Where::init()->where('id', '>', 50), 'id', 'asc') would return array(51,52,53,54,55,...)
	 * @link http://koldy.net/docs/database/models#fetchArrayOf
	 */
	public static function fetchArrayOf($field, $where = null, $orderField = null, $orderDirection = null, $limit = null) {
		$select = static::query()->field($field, 'key_field');

		if ($where !== null) {
			if ($where instanceof Where) {
				$select->where($where);
			} else if (is_array($where)) {
				foreach ($where as $field => $value) {
					$select->where($field, $value);
				}
			} else if (!is_array(static::$primaryKey) && (is_numeric($where) || is_string($where))) {
				$select->where(static::$primaryKey, $where);
			}
		}

		if ($orderField !== null) {
			$select->orderBy($orderField, $orderDirection);
		}

		if ($limit !== null) {
			$select->limit(0, $limit);
		}

		$records = $select->fetchAll();
		$data = array();

		foreach ($records as $r) {
			$data[] = $r['key_field'];
		}

		return $data;
	}


	/**
	 * Fetch only one record and return value from given column
	 * 
	 * @param string $field
	 * @param mixed $where [optional]
	 * @param string $orderField [optional]
	 * @param string $orderDirection [optional]
	 * @return mixed or false if record wasn't found
	 */
	public static function fetchOneValue($field, $where = null, $orderField = null, $orderDirection = null) {
		$select = static::query()->field($field, 'key_field')->limit(0, 1);

		if ($where !== null) {
			if ($where instanceof Where) {
				$select->where($where);
			} else if (is_array($where)) {
				foreach ($where as $field => $value) {
					$select->where($field, $value);
				}
			} else if (!is_array(static::$primaryKey) && (is_numeric($where) || is_string($where))) {
				$select->where(static::$primaryKey, $where);
			}
		}

		if ($orderField !== null) {
			$select->orderBy($orderField, $orderDirection);
		}

		$records = $select->fetchAll();
		
		if (sizeof($records) > 0) {
			return $records[0]['key_field'];
		}

		return false;
	}


	/**
	 * Check if some value exists in database or not. This is useful if you
	 * want, for an example, check if user's e-mail already is in database
	 * before you try to insert your data.
	 * 
	 * @param  string $field
	 * @param  mixed $value
	 * @param  mixed $exceptionValue OPTIONAL
	 * @param  string $exceptionField OPTIONAL
	 * @link http://koldy.net/docs/database/models#isUnique
	 * 
	 * @example
	 * 		User::isUnique('email', 'email@domain.com'); will execute:
	 *   	SELECT COUNT(*) FROM user WHERE email = 'email@domain.com'
	 * 
	 * 		User::isUnique('email', 'email@domain.com', 'other@mail.com');
	 *   	SELECT COUNT(*) FROM user WHERE email = 'email@domain.com' AND email != 'other@mail.com'
	 * 
	 * 		User::isUnique('email', 'email@domain.com', 5, 'id');
	 *   	SELECT COUNT(*) FROM user WHERE email = 'email@domain.com' AND id != 5
	 */
	public static function isUnique($field, $value, $exceptionValue = null, $exceptionField = null) {
		$select = static::query();
		$select->field('COUNT(*)', 'total')
			->where($field, $value);

		if ($exceptionValue !== null) {
			if ($exceptionField === null) {
				$exceptionField = $field;
			}

			$select->where($exceptionField, '!=', $exceptionValue);
		}

		$results = $select->fetchAllObj();

		if (isset($results[0])) {
			return ($results[0]->total == 0);
		}

		return null;
	}


	/**
	 * Count the records in table according to the parameters
	 * 
	 * @param array $what
	 * @return int
	 * @link http://koldy.net/docs/database/models#count
	 */
	public static function count($where = null) {
		$select = static::query();

		if ($where !== null) {
			if ($where instanceof Where) {
				$select->field('COUNT(*)', 'total');
				$select->where($where);

			} else if (is_array($where)) {
				$select->field('COUNT(*)', 'total');
				foreach ($where as $field => $value) {
					$select->where($field, $value);
				}

			} else if (!is_array(static::$primaryKey) && (is_numeric($where) || is_string($where))) {
				$select->field('COUNT(' . static::$primaryKey . ')', 'total');
				$select->where(static::$primaryKey, $where);

			}
		} else {
			$pk = is_string(static::$primaryKey) ? static::$primaryKey : '*';
			$select->field('COUNT(' . $pk . ')', 'total');
		}

		$results = $select->fetchAllObj();

		if (isset($results[0])) {
			$r = $results[0];
			if (property_exists($r, 'total')) {
				return (int) $r->total;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}


	/**
	 * Get the ResultSet object of this model
	 * 
	 * @return \Koldy\Db\ResultSet
	 */
	public static function resultSet() {
		$rs = new ResultSet(static::getTableName());
		$rs->setConnection(static::$connection);
		return $rs;
	}


	/**
	 * Get the initialized Select object with populated FROM part
	 * 
	 * @return \Koldy\Db\Select
	 */
	public static function query() {
		$select = new Select(static::getTableName());
		$select->setConnection(static::$connection);
		return $select;
	}


	public function __toString() {
		return \Koldy\Json::encode($this->getData());
	}

}
