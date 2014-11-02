<?php namespace Koldy\Db;

use Koldy\Db;
use Koldy\Exception;

/**
 * This is abstract class that should extend every Query Builder class. This
 * class knows how to execute and debug your query from parent class.
 * 
 */
abstract class Query {


	/**
	 * The connection name on which this query will be executed
	 * 
	 * @var string
	 */
	private $connection = null;


	/**
	 * The binding values for PDO
	 * 
	 * @var array
	 */
	protected $bindings = array();


	/**
	 * The keyIndex is counter for field bindings. Thanks to this, you're able
	 * to bind multiple values on same field in your SQL command. This is
	 * framework's internal variable so don't use it.
	 * 
	 * @var int
	 */
	public static $keyIndex = 0;


	/**
	 * Get the query and populate bindings array
	 * 
	 * @return string
	 */
	abstract protected function getQuery();


	/**
	 * Get bindings for PDO
	 * 
	 * @return array
	 */
	public function getBindings() {
		return $this->bindings;
	}


	/**
	 * Get next key index
	 *
	 * @return int
	 */
	protected static function getKeyIndex() {
		if (static::$keyIndex === PHP_INT_MAX) {
			static::$keyIndex = 0;
		} else {
			static::$keyIndex++;
		}

		return static::$keyIndex;
	}


	/**
	 * Set adapter connection's name
	 * 
	 * @param string $connection
	 * @return \Koldy\Db\Query
	 */
	public function setConnection($connection) {
		$this->connection = $connection;
		return $this;
	}


	/**
	 * Get the connection name
	 * 
	 * @return string|null if default connection will be used
	 */
	public function getConnection() {
		return $this->connection;
	}


	/**
	 * Get the database adapter
	 * 
	 * @return \Koldy\Db\Adapter
	 */
	public function getAdapter() {
		return Db::getAdapter($this->connection);
	}


	/**
	 * Execute the query
	 * 
	 * @param string|\Koldy\Db\Adapter $adapter [optional] execute this query on which adapter?
	 * @throws \Koldy\Exception
	 * @return array|int number of affected rows or array of resultset returned by database
	 */
	public function exec($adapter = null) {
		$query = $this->getQuery();

		if ($query === null) {
			throw new Exception('SQL not built, can not execute query');
		}

		if ($adapter === null) {
			$adapter = $this->getAdapter();
		} else if (is_string($adapter)) {
			$adapter = Db::getAdapter($adapter);
		} else if ($adapter instanceof Adapter) {
			// then its fine, don't do anything, just continue
		} else {
			throw new Exception('Invalid DB adapter');
		}

		return $adapter->query($query, $this->getBindings());
	}


	/**
	 * Return some debug informations about the query you built
	 * 
	 * @param bool $oneLine return query in one line
	 * @return string
	 */
	public function debug($oneLine = false) {
		$query = $this->__toString();
		$bindings = '';

		foreach ($this->bindings as $key => $value) {
			if ($key[0] == ':') {
				$key = substr($key, 1);
			}

			if (is_numeric($value) && substr((string) $value, 0, 1) != '0') {
				$value = (string) $value;

				if (strlen($value) > 10) {
					$query = str_replace(":{$key}", "'{$value}'", $query);
				} else {
					$query = str_replace(":{$key}", $value, $query);
				}

			} else {
				$query = str_replace(":{$key}", ("'" . addslashes($value) . "'"), $query);
			}
		}

		if ($oneLine) {
			$query = str_replace("\t", '', $query);
			$query = str_replace("\n", ' ', $query);
			$query = str_replace('  ', ' ', $query);
		}

		return $query;
	}


	/**
	 * If printing query builder instance, then just show the generated SQL
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->getQuery();
	}

}
