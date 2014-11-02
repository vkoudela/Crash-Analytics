<?php namespace Koldy\Log\Writer;

use Koldy\Application;
use Koldy\Cli;
use Koldy\Convert;
use Koldy\Mail;
use Koldy\Request;
use Koldy\Server;

/**
 * If you plan to create your own log writer, then please extend this class and
 * then do whatever you want to.
 *
 * @link http://koldy.net/docs/log
 */
abstract class AbstractLogWriter {


	/**
	 * The config array got from 'options' part in config/application.php
	 * 
	 * @var array
	 */
	protected $config = null;


	/**
	 * The array of last X messages (by default, the last 100 messages)
	 * 
	 * @var array
	 */
	private $messages = array();


	/**
	 * To send email report or not
	 * 
	 * @var boolean
	 */
	private $emailReport = false;


	/**
	 * Constructor
	 * 
	 * @param array $config
	 */
	public function __construct(array $config) {
		$this->config = $config;
	}
	
	/**
	 * Handle message logging
	 * 
	 * @param string $level
	 * @param mixed $message
	 */
	abstract protected function logMessage($level, $message);


	/**
	 * Write DEBUG message to log
	 *
	 * @param string $message
	 * @link http://koldy.net/docs/log#usage
	 */
	public function debug($message) {
		$this->logMessage('debug', $message);
	}


	/**
	 * Write NOTICE message to log
	 *
	 * @param string $message
	 * @link http://koldy.net/docs/log#usage
	 */
	public function notice($message) {
		$this->logMessage('notice', $message);
	}


	/**
	 * Write SQL message to log
	 *
	 * @param string $query
	 * @link http://koldy.net/docs/log#usage
	 */
	public function sql($query) {
		$this->logMessage('sql', $query);
	}


	/**
	 * Write INFO message to log
	 *
	 * @param string $message
	 * @link http://koldy.net/docs/log#usage
	 */
	public function info($message) {
		$this->logMessage('info', $message);
	}


	/**
	 * Write WARNING message to log
	 *
	 * @param string $message
	 * @link http://koldy.net/docs/log#usage
	 */
	public function warning($message) {
		$this->logMessage('warning', $message);
	}


	/**
	 * Write ERROR message to log
	 *
	 * @param string $message
	 * @link http://koldy.net/docs/log#usage
	 */
	public function error($message) {
		$this->logMessage('error', $message);
	}


	/**
	 * Write EXCEPTION message to log
	 *
	 * @param \Exception $e
	 * @link http://koldy.net/docs/log#usage
	 */
	public function exception(\Exception $e) {
		$this->logMessage('exception', "Exception in {$e->getFile()}:{$e->getLine()}\n\n{$e->getMessage()}\n\n{$e->getTraceAsString()}");
	}


	/**
	 * Append log message to the request's scope
	 * 
	 * @param string $message
	 */
	protected function appendMessage($message) {
		$this->messages[] = $message;

		if (sizeof($this->messages) > 100) {
			array_shift($this->messages);
		}
	}


	/**
	 * Detect if e-mail alert should be sent
	 * 
	 * @param string $level
	 */
	protected function detectEmailAlert($level) {
		if ($this->emailReport === false && $this->config['email'] !== null && in_array($level, $this->config['email_on'])) {
			$this->emailReport = true;
		}
	}


	/**
	 * Override this method if you have anything you need to do on
	 * request shutdown except of just sending e-mail alerts
	 */
	public function shutdown() {
		$this->sendEmailReport();
	}


	/**
	 * Process extended reports
	 */
	protected function processExtendedReports() {}


	/**
	 * Send e-mail report if system detected that e-mail should be sent
	 * 
	 * @return boolean|null true if mail was sent and null if mail shouldn't be sent
	 */
	protected function sendEmailReport() {
		if ($this->emailReport === true && $this->config['email'] !== null) {
			$body = implode('', $this->messages);

			/* this doesn't have sense any more :::
			$body .= "\n\n---------- debug_backtrace:\n";

			foreach (debug_backtrace() as $r) {
				if (isset($r['file']) && isset($r['line'])) {
					$body .= "{$r['file']}:{$r['line']} ";
				}

				if (isset($r['function'])) {
					$body .= "{$r['function']} ";
				}

				if (isset($r['args'])) {
					$body .= implode(', ', $r['args']);
				}

				$body .= "\n";
			}
			*/
			
			$body .= "\n----------\n";
			$body .= sprintf("server: %s (%s)\n", Request::serverIp(), Request::hostName());

			if (PHP_SAPI != 'cli') {
				$body .= 'URI: ' . $_SERVER['REQUEST_METHOD'] . '=' . Application::getConfig('application', 'site_url') . Application::getUri() . "\n";
				$body .= sprintf("User IP: %s (%s)%s", Request::ip(), Request::host(), (Request::hasProxy() ? sprintf(" via %s for %s\n", Request::proxySignature(), Request::httpXForwardedFor()) : "\n"));
				$body .= sprintf("UAS: %s\n", (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'no user agent set'));
			} else {
				$body .= 'CLI Name: ' . Application::getCliName() . "\n";
				$body .= 'CLI Script: ' . Application::getCliScript() . "\n";

				$params = Cli::getParameters();
				if (count($params) > 0) {
					$body .= 'CLI Params: ' . print_r($params, true) . "\n";
				}
			}

			$body .= sprintf("Server load: %s\n", Server::getServerLoad());

			$peak = memory_get_peak_usage(true);
			$memoryLimit = ini_get('memory_limit');

			$body .= sprintf("Memory: %s; peak: %s; limit: %s; spent: %s%%\n",
				Convert::bytesToString(memory_get_usage(true)),
				Convert::bytesToString($peak),
				$memoryLimit,
				($memoryLimit !== false && $memoryLimit > 0 ? round($peak * 100 / Convert::stringToBytes($memoryLimit), 2) : 'null')
			);

			$body .= sprintf("included files: %s\n", print_r(get_included_files(), true));

			$mail = Mail::create();
			$mail
				->from('alert@' . Request::hostName(), Request::hostName())
				->subject('Log report')
				->body($body);

			if (!is_array($this->config['email']) && strpos($this->config['email'], ',') !== false) {
				$this->config['email'] = explode(',', $this->config['email']);
			}

			if (is_array($this->config['email'])) {
				foreach ($this->config['email'] as $toEmail) {
					$mail->to(trim($toEmail));
				}
			} else {
				$mail->to(trim($this->config['email']));
			}

			if (!$mail->send()) {
				$this->error("Can not send alert mail to {$this->config['email']}: {$mail->getError()}\n{$mail->getException()->getTraceAsString()}");
				return false;
			}

			return true;
		}

		return null;
	}

}
