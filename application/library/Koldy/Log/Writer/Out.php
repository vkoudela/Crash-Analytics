<?php namespace Koldy\Log\Writer;

use Koldy\Application;

/**
 * This log writer will print all messages to console. This writer is made to
 * be used in CLI environment.
 * 
 * @link http://koldy.net/docs/log/out
 *
 */
class Out extends AbstractLogWriter {

	/**
	 * Get message function handler
	 *
	 * @var function
	 */
	protected $getMessageFunction = null;


	/**
	 * Construct the handler to log to files. The config array will be check
	 * because all configs are strict
	 *
	 * @param array $config
	 */
	public function __construct(array $config) {
		if (!isset($config['log']) || !is_array($config['log'])) {
			throw new Exception('You must define \'log\' levels in file log driver config options at least with empty array');
		}

		if (isset($config['email_on']) && !is_array($config['email_on'])) {
			throw new Exception('If \'email_on\' is defined, then it has to be an array');
		}

		if (!isset($config['email_on'])) {
			$config['email_on'] = array();
		}

		if (!array_key_exists('email', $config)) {
			$config['email'] = null;
		}

		if (!isset($config['dump'])) {
			$config['dump'] = array();
		}

		if (isset($config['get_message_fn'])) {
			if (is_object($config['get_message_fn']) && $config['get_message_fn'] instanceof \Closure) {
				$this->getMessageFunction = $config['get_message_fn'];
			} else {

				if (is_object($config['get_message_fn'])) {
					$got = get_class($config['get_message_fn']);
				} else {
					$got = gettype($config['get_message_fn']);
				}

				throw new Exception('Invalid get_message_fn type; expected \Closure object, got: ' . $got);
			}
		}

		parent::__construct($config);
	}


	/**
	 * Get the message that will be printed in console. You have to return the
	 * whole line including the time if you want. This is default, but you can
	 * override this method.
	 * 
	 * @param string $level
	 * @param string $message
	 * @return string
	 */
	protected function getMessage($level, $message) {
		if ($this->getMessageFunction !== null) {
			return call_user_func($this->getMessageFunction, $level, $message);
		}

		return date('Y-m-d H:i:sO') . "\t{$level}\t{$message}\n";
	}


	/**
	 * Actually print message out
	 * 
	 * @param string $level
	 * @param string $message
	 * @throws \Koldy\Exception
	 */
	protected function logMessage($level, $message) {
		if (is_object($message)) {
			$message = $message->__toString();
		}

		$logMessage = $this->getMessage($level, $message);

		if ($logMessage !== false) {
			if (in_array($level, $this->config['log'])) {
				print $logMessage;
			}

			$this->detectEmailAlert($level);
			$this->appendMessage($logMessage);
		}
	}


	/**
	 * This method is called internally.
	 */
	public function shutdown() {
		$this->processExtendedReports();
		$this->sendEmailReport();
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Log\Writer\AbstractLogWriter::processExtendedReports()
	 */
	protected function processExtendedReports() {
		if (!isset($this->config['dump'])) {
			return;
		}
	
		$dump = $this->config['dump'];
	
		// 'speed', 'included_files', 'include_path', 'whitespace'
	
		if (in_array('speed', $dump)) {
			$method = isset($_SERVER['REQUEST_METHOD'])
			? ($_SERVER['REQUEST_METHOD'] . '=' . Application::getUri())
			: ('CLI=' . Application::getCliName());
	
			$executedIn = Application::getRequestExecutionTime();
			$this->logMessage('notice', $method . ' LOADED IN ' . $executedIn . 'ms, ' . count(get_included_files()) . ' files');
		}
	
		if (in_array('included_files', $dump)) {
			$this->logMessage('notice', print_r(get_included_files(), true));
		}
	
		if (in_array('include_path', $dump)) {
			$this->logMessage('notice', print_r(explode(':', get_include_path()), true));
		}
	
		if (in_array('whitespace', $dump)) {
			$this->logMessage('notice', "----------\n\n\n");
		}
	}
}
