<?php namespace Koldy;

use Koldy\Db\Adapter;

/**
 * The main Db class.
 *
 * @link http://koldy.net/docs/database/configuration
 */
class Db {


	/**
	 * Config array from public/config/database.php
	 * 
	 * @var array
	 */
	private static $config = null;


	/**
	 * The array of initialized adapters
	 * 
	 * @var array
	 */
	private static $adapter = array();


	/**
	 * The array of adapters that will be tried to use. If connection parameters
	 * doesn't exists, the next one will be tried
	 * 
	 * @var array
	 */
	private static $defaultKeys = null;


	/**
	 * The default adapter key (automatically detected)
	 * 
	 * @var string
	 */
	private static $defaultKey = null;


	/**
	 * Initialize the config for database(s)
	 */
	public static function init() {
		if (static::$config === null) {
			static::$config = Application::getConfig('database');
			$keys = array_keys(static::$config);
			static::$defaultKey = $keys[0];
		}
	}


	/**
	 * Get adapter. If parameter not set, default is returned
	 * 
	 * @param string $whatAdapter [optional]
	 * @return \Koldy\Db\Adapter
	 */
	public static function getAdapter($whatAdapter = null) {
		static::init();

		if ($whatAdapter === null) {
			if (static::$defaultKeys === null) {
				$adapter = static::$defaultKey;
			} else {
				$adapter = null;
				foreach (static::$defaultKeys as $adapterName) {
					if ($adapter === null && isset(static::$config[$adapterName])) {
						$adapter = $adapterName;
					}
				}

				if ($adapter === null) {
					$adapter = static::$defaultKey;
				}
			}
		} else {
			$adapter = $whatAdapter;
		}

		if (!isset(static::$adapter[$adapter])) {
			$config = static::$config[$adapter];
			static::$adapter[$adapter] = new Adapter($config, $adapter);
		}

		return static::$adapter[$adapter];
	}


	/**
	 * Add adapter manually to the list of registered adapters
	 * 
	 * @param string $name
	 * @param array $config
	 */
	public static function addAdapter($name, array $config) {
		static::$config[$name] = $config;
	}


	/**
	 * Set the array of default adapter keys. This is useful if you're using adapter
	 * keys that sometimes are not registered. In that case, system will try to lookup
	 * for next adapter key.
	 * 
	 * @param array $defaultKeys
	 */
	public static function setDefaultKeys(array $defaultKeys) {
		static::$defaultKeys = $defaultKeys;
	}


	/**
	 * Get the default key
	 * 
	 * @return string
	 */
	public static function getDefaultKey() {
		return static::$defaultKey;
	}


	/**
	 * Execute the query on default adapter
	 * 
	 * @param string $query
	 * @param array $bindings
	 * @param integer $fetchMode pass only PDO::FETCH_* constants
	 * @return mixed
	 */
	public static function query($query, array $bindings = null, $fetchMode = null) {
		$adapter = static::getAdapter();
		return $adapter->query($query, $bindings, $fetchMode);
	}


	/**
	 * Create new object for INSERT query
	 * 
	 * @param string $intoTable
	 * @param array $rowValues is key => value array to insert into database
	 * @return \Koldy\Db\Insert
	 * @link http://koldy.net/docs/database/query-builder#insert
	 */
	public static function insert($intoTable = null, array $rowValues = null) {
		return new Db\Insert($intoTable, $rowValues);
	}


	/**
	 * Create new SELECT query
	 * 
	 * @param string $fromTable
	 * @param array $fields
	 * @return \Koldy\Db\Select
	 * @link http://koldy.net/docs/database/query-builder#select
	 */
	public static function select($fromTable = null, array $fields = null) {
		$select = new Db\Select($fromTable);

		if ($fields !== null) {
			$select->fields($fields);
		}

		return $select;
	}


	/**
	 * Create new object for UPDATE query
	 * 
	 * @param string $whatTable
	 * @param array $values
	 * @return \Koldy\Db\Update
	 * @link http://koldy.net/docs/database/query-builder#update
	 */
	public static function update($whatTable, array $values = null) {
		return new Db\Update($whatTable, $values);
	}


	/**
	 * Create new object for DELETE query
	 * 
	 * @param string $tableName
	 * @return \Koldy\Db\Delete
	 * @link http://koldy.net/docs/database/query-builder#delete
	 */
	public static function delete($fromTable) {
		return new Db\Delete($fromTable);
	}


	/**
	 * Get the last executed query
	 * 
	 * @param string $connection
	 * @return string
	 */
	public static function getLastQuery($connection = null) {
		return static::getAdapter($connection)->getLastQuery();
	}


	/**
	 * Get the last insert ID
	 * 
	 * @param string $connection
	 * @return int
	 */
	public static function lastInsertId($connection = null) {
		return static::getAdapter($connection)->getLastInsertId();
	}


	/**
	 * Get the last error
	 * 
	 * @param string $connection
	 * @return string
	 */
	public static function getLastError($connection = null) {
		return static::getAdapter($connection)->getLastError();
	}


	/**
	 * Close connection
	 * 
	 * @return \Koldy\Db\Adapter
	 */
	public function close() {
		$this->pdo = null;
		return $this;
	}

	/**
	 * Reconnect to server
	 */
	public function reconnect() {
		$this->close()
			->getAdapter()
			->prepare('SELECT 1')
			->execute();
	}


	/**
	 * Get raw expression string
	 * 
	 * @param string $expr
	 * @return \Koldy\Db\Expr
	 */
	public static function expr($expr) {
		return new Db\Expr($expr);
	}

}
