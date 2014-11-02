<?php namespace Koldy\Log\Writer;

use Koldy\Application;
use Koldy\Exception;
use Koldy\Request;
use Koldy\Directory;

/**
 * This log writer will print all log messages into file on file system. Its
 * smart enough to open the file just once and close it when request execution
 * completes.
 * 
 * @link http://koldy.net/docs/log/file
 */
class File extends AbstractLogWriter {


	/**
	 * The file pointer
	 * 
	 * @var resource
	 */
	private $fp = null;


	/**
	 * The last file pointer file name for log
	 * 
	 * @var string
	 */
	private $fpFile = null;


	/**
	 * Flag if file pointer was already closed
	 * 
	 * @var boolean
	 */
	private $closed = false;


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
		if (!array_key_exists('path', $config)) {
			throw new Exception('You must define \'path\' in file log driver config options at least with null');
		}

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
	 * Get the message that will be logged into file
	 * 
	 * @param string $level
	 * @param string $message
	 * @return string
	 */
	protected function getMessage($level, $message) {
		if ($this->getMessageFunction !== null) {
			return call_user_func($this->getMessageFunction, $level, $message);
		}

		$ip = Request::ip();
		return date('Y-m-d H:i:sO') . "\t{$level}\t{$ip}\t{$message}\n";
	}


	/**
	 * Get the name of log file
	 * 
	 * @return string
	 */
	protected function getFileName() {
		return date('Y-m-d') . '.log';
	}


	/**
	 * Actually log message to file
	 * 
	 * @param string $level
	 * @param string $message
	 * @throws \Koldy\Exception
	 */
	protected function logMessage($level, $message) {
		// If script is running for very long time (e.g. CRON), then date might change if time passes midnight.
		// In that case, log will continue to write messages to new file.
			
		$fpFile = $this->getFileName();
		if ($fpFile !== $this->fpFile) {
			//date has changed? or we need to init?
			if ($this->fp) {
				// close pointer to old file
				@fclose($this->fp);
			}

			if ($this->config['path'] === null) {
				$path = Application::getStoragePath('log' . DS . $fpFile);
			} else {
				$path = str_replace(DS.DS, DS, $this->config['path'] . DS . $fpFile);
			}

			$this->fpFile = $fpFile;

			if (!($this->fp = @fopen($path, 'a'))) {
				// file failed to open, maybe directory doesn't exists?
				$dir = dirname($path);
				if (!is_dir($dir)) {
					if (!Directory::mkdir($dir, 0755)) {
						throw new Exception('Can not create directory on location=' . $dir);
					}
				}

				if (!($this->fp = @fopen($path, 'a'))) {
					// now the directory should exists, so try to open file again
					throw new Exception('Can not write to log file on path=' . $path);
				}
			}
		}
	
		if (!$this->fp || $this->fp === null) {
			throw new Exception('Can not write to log file');
		}
	
		if (is_object($message)) {
			$message = $message->__toString();
		}
	
		$logMessage = $this->getMessage($level, $message);

		if ($logMessage !== false) {
			if (in_array($level, $this->config['log'])) {
				if (!@fwrite($this->fp, $logMessage)) { // actually write it in file
					throw new Exception('Unable to write to log file');
				} else {

					// so, writing was ok, but what if showdown was already called?
					// then we'll close the file, but additional email alerts won't
					// be sent any more - sorry

					if ($this->closed) {
						@fclose($this->fp);
						$this->fp = null;
						$this->fpFile = null;
					}
				}
			}

			$this->appendMessage($logMessage);
			$this->detectEmailAlert($level);
		}
	}


	/**
	 * This method is called internally.
	 */
	public function shutdown() {
		$this->processExtendedReports();
		$this->sendEmailReport();
		
		if ($this->fp !== null) {
			@fclose($this->fp);

			$this->fp = null;
			$this->fpFile = null;
			$this->closed = true;
		}
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
