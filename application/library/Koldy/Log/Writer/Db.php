<?php namespace Koldy\Log\Writer;

use Koldy\Exception;
use Koldy\Db\Insert;
use Koldy\Db as KoldyDb;

/**
 * This log writer will insert your log messages into database.
 * For MySQL, we're recommending this table structure:
 * 
 * 		CREATE TABLE `log` (
 * 			`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 * 			 `time` timestamp NOT NULL,
 * 			 `level` enum('debug','notice','sql','info','warning','error','exception') NOT NULL,
 * 			 `message` mediumtext CHARACTER SET utf16,
 * 			 PRIMARY KEY (`id`),
 * 			 KEY `time` (`time`),
 * 			 KEY `level` (`level`)
 * 		) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 *
 * Ofcourse, you can have your own table structure with totally different column
 * names, but then you need to define get_data_fn function in options for
 * this writer.
 * 
 * @link http://koldy.net/docs/log/db
 */
class Db extends AbstractLogWriter {


	/**
	 * The flag if query is being inserted into database to prevent recursion
	 * 
	 * @var boolean
	 */
	private $inserting = false;


	/**
	 * Construct the DB writer
	 * 
	 * @param array $config
	 * @throws Exception
	 */
	public function __construct(array $config) {
		if (!isset($config['table'])) {
			throw new Exception('Undefined \'table\' in DB writer options');
		}

		if (!array_key_exists('connection', $config)) {
			throw new Exception('Undefined \'connection\' in DB writer options');
		}

		if (isset($config['get_data_fn']) && !is_callable($config['get_data_fn'])) {
			throw new Exception('get_data_fn in DB writer options is not callable');
		}

		if (!isset($config['email'])) {
			$config['email'] = null;
		}

		parent::__construct($config);
	}


	/**
	 * Get array of field=>value to be inserted in log table
	 * 
	 * @param string $level
	 * @param string $message
	 * @throws \Koldy\Exception
	 * @return array
	 */
	protected function getFieldsData($level, $message) {
		if (isset($this->config['get_data_fn'])) {
			$data = call_user_func($this->config['get_data_fn'], $level, $message);

			if (!is_array($data)) {
				throw new Exception('DB driver config get_data_fn function must return an array; ' . gettype($data) . ' given');
			}

			return $data;
		}

		return array(
			'time' => time(),
			'level' => $level,
			'message' => $message
		);
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Log\Writer\AbstractLogWriter::logMessage()
	 */
	protected function logMessage($level, $message) {
		if ($this->inserting) {
			return;
		}

		$data = $this->getFieldsData($level, $message);

		if ($data !== false) {
			if (in_array($level, $this->config['log'])) {
				$this->inserting = true;

				$insert = new Insert($this->config['table']);
				$insert->add($data);

				if ($insert->exec($this->config['connection']) === false) {
					$adapter = KoldyDb::getAdapter($this->config['connection']);
					// do not process this with Log::exception because it will run into recursion
					$this->detectEmailAlert('exception');
					$this->appendMessage(date('Y-m-d H:i:sO') . "\tERROR inserting log message into database: {$adapter->getLastError()}\n\n{$adapter->getLastException()->getTraceAsString()}\n");
				}
			}

			$this->inserting = false;

			$this->detectEmailAlert($level);
			$this->appendMessage(date('Y-m-d H:i:sO') . "\t" . implode("\t", array_values($data)) . "\n");
		}
	}

}
