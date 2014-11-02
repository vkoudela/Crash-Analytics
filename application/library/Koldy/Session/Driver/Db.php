<?php namespace Koldy\Session\Driver;

use Koldy\Db as KoldyDb;
use Koldy\Db\Select;
use Koldy\Db\Insert;
use Koldy\Db\Update;
use Koldy\Db\Delete;
use Koldy\Exception;
use Koldy\Log;

/**
 * This is session handler that will store session data into database.
 * 
 * You MUSTN'T use it! This class will use PHP internally by it self. You just
 * configure it all and watch the magic.
 * 
 * The table structure is:
 * 
 * CREATE TABLE `session` (
 *  `id` varchar(40) NOT NULL,
 *  `time` int(10) unsigned NOT NULL,
 *  `data` text CHARACTER SET utf16 NOT NULL,
 *  PRIMARY KEY (`id`),
 *  KEY `last_activity_for_gc` (`time`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 *
 * @link http://koldy.net/docs/session/db
 */
class Db implements \SessionHandlerInterface {


	/**
	 * The 'options' part from config/session.php
	 * 
	 * @var array
	 */
	protected $config = array();


	/**
	 * Construct the Db Session storage handler
	 * 
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		$this->config = $config;
	}


	/**
	 * (non-PHPdoc)
	 * @see SessionHandlerInterface::open()
	 */
	public function open($save_path, $sessionid) {
		if (!array_key_exists('connection', $this->config)) {
			throw new Exception('Connection parameter is not defined in session\'s DB driver options');
		}

		if (!isset($this->config['table'])) {
			throw new Exception('Table parameter is not defined in session\'s DB driver options');
		}

		return true;
	}


	/**
	 * (non-PHPdoc)
	 * @see SessionHandlerInterface::close()
	 */
	public function close() {
		return true;
	}


	/**
	 * Get the session data from database
	 * 
	 * @param string $sessionid
	 * @return \stdClass|false if data doesn't exist in database
	 */
	private function getDbData($sessionid) {
		$select = new Select($this->config['table']);
		$select
			->field('time', 'time')
			->field('data', 'data')
			->where('id', $sessionid)
			->setConnection($this->config['connection']);
		
		return $select->fetchFirstObj();
	}


	/**
	 * (non-PHPdoc)
	 * @see SessionHandlerInterface::read()
	 */
	public function read($sessionid) {
		$sess = $this->getDbData($sessionid);

		if ($sess === false) {
			return '';
		} else {
			return $sess->data;
		}
	}


	/**
	 * (non-PHPdoc)
	 * @see SessionHandlerInterface::write()
	 */
	public function write($sessionid, $sessiondata) {
		$data = array(
			'time' => time(),
			'data' => $sessiondata
		);

		$sess = $this->getDbData($sessionid);

		if ($sess === false) {
			// the record doesn't exists in database, lets insert it
			$data['id'] = $sessionid;

			$insert = new Insert($this->config['table']);
			$insert->add($data);
			if ($insert->exec($this->config['connection']) === false) {
				Log::error('Error inserting session data into session table. Data=' . print_r($data, true));
				return false;
			}
		} else {
			// the record data already exists in db

			$update = new Update($this->config['table']);
			$update->setValues($data)->where('id', $sessionid);
			if ($update->exec($this->config['connection']) === false) {
				Log::error("Error update session data in session table for id={$sessionid} and data=" . print_r($data, true));
				return false;
			}
		}

		return true;
	}


	/**
	 * (non-PHPdoc)
	 * @see SessionHandlerInterface::destroy()
	 */
	public function destroy($sessionid) {
		$delete = new Delete($this->config['table']);
		if ($delete->where('id', $sessionid)->exec($this->config['connection']) === false) {
			Log::error("Error deleting session record from database under id={$sessionid}");
			return false;
		}

		return true;
	}


	/**
	 * (non-PHPdoc)
	 * @see SessionHandlerInterface::gc()
	 */
	public function gc($maxlifetime) {
		$delete = new Delete($this->config['table']);
		$timestamp = time() - $maxlifetime;
		if ($delete->where('time', '<', $timestamp)->exec($this->config['connection']) === false) {
			Log::error("Session GC: Error deleting session record from database that are older then maxlifetime={$maxlifetime} (older then timestamp {$timestamp})");
			return false;
		}

		return true;
	}

}
