<?php
/**
 * This array contains the list of database adapter settings so query can be executed
 * on any adapter you define here.
 * 
 * If you want to work with other database connection on your model, then use protected
 * static property "$connection", such as:
 * 
 * 		class User extends \Koldy\Db\Model {
 *   		protected static $connection = 'second-connection';
 *   	}
 * 
 * @link http://koldy.net/docs/database/configuration
 */
return array(


	/**
	 * The first connection is the default. You can get this by calling Db::getAdapter()
	 */
	'site' => array(
		'type' => 'mysql',
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'test',
		'persistent' => true,
		'charset' => 'utf8'
	),


	/**
	 * The second connection you may use. You can get this by calling Db::getAdapter('second-connection')
	 * 
	 * @link http://koldy.net/docs/database/configuration#connections
	 */
	'second-connection' => array(
		'type' => 'mysql',
		'host' => '192.168.1.2',
		'username' => 'root',
		'password' => '',
		'database' => 'test',
		'persistent' => false,
		'charset' => 'utf8'
	),


	/**
	 * The connection with backups - if first one fails, then framework will
	 * keep trying to connect by the order defined in backup_connections until it connects
	 * 
	 * @link http://koldy.dev/docs/database/configuration#failover
	 */
	'connection-with-backups-example' => array(
		'type' => 'mysql',
		'host' => '192.168.1.2',
		'username' => 'root',
		'password' => '',
		'database' => 'test',
		'persistent' => false,
		'charset' => 'utf8',
		'driver_options' => array(
			// if connection is not opened within 3 seconds, try using backup connection
			PDO::ATTR_TIMEOUT => 3
		),

		'backup_connections' => array(
			array(
				'log_error' => false,
				'wait_before_connect' => 500, // in miliseconds
				'type' => 'mysql',
				'host' => '192.168.1.3',
				'username' => 'root',
				'password' => '',
				'database' => 'test',
				'persistent' => true,
				'charset' => 'utf8'
			),
			array(
				'log_error' => true,
				'type' => 'mysql',
				'host' => '192.168.1.4',
				'username' => 'root',
				'password' => '',
				'database' => 'test',
				'persistent' => true,
				'charset' => 'utf8'
			)
		)
	)

);
