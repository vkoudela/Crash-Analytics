<?php namespace Koldy;

use Koldy\Application;
use Koldy\Log\Writer\AbstractLogWriter;

/**
 * Class to handle the log and writing to log. Be aware that using too much of log slows down the
 * complete application, while other processes are waiting to finish your log. Ofcourse, you can rapidly optimze
 * this by using slightly different syntax. As you already know, if you have your log disabled, but you're still
 * calling for an example Log::info("User {$fullName} has logged in"); the PHP interpreter will still have to
 * parse the passing string and then inside of info() method, message will be disregarded. To avoid this, you can
 * use the following syntax:
 * 
 * @example		if (LOG) Log::info("User {$fullName} has logged in");
 * 
 * The LOG constant will be true only if any of log drivers are enabled, otherwise it will always be false.
 *
 * You are encouraged to use log in development, but reduce logs in production mode as much as you can. Always
 * log only important data and never log the code that will always execute successfully.
 *
 * Using file output, this singleton instance will open the file on the first call and won't be closed. Instead
 * of closing, class will register itself in application's shutdown proceses and will close the log file when
 * request finish its job. That way log file won't be opened and closed every time you want to log something.
 *
 * If you have enabled email logging, then this script will send you log message(s) to your error mail. To reduce
 * SPAM, if there are a lot of error messages to send, all other log messages will be mailed at once as well. Lets
 * say you have 5 info log messages, 1 notice and 1 error - you'll receive error mail with all messages logged
 * with Log class even if those message won't be written to your Log driver.
 *
 * @link http://koldy.net/docs/log
 * 
 */
class Log {


	/**
	 * The array of only enabled writer instances for this request
	 * 
	 * @var array
	 */
	private static $writers = null;


	protected function __construct() {}
	protected function __clone() {}


	/**
	 * Initialize, load config and etc.
	 */
	private static function init() {
		if (static::$writers === null) {
			static::$writers = array();
			$configs = Application::getConfig('application', 'log');

			$count = 0;
			foreach ($configs as $config) {
				if ($config['enabled']) {
					// if the config is enabled, then make new instance

					$writer = $config['writer_class'];
					static::$writers[$count] = new $writer($config['options']);

					if (!(static::$writers[$count] instanceof AbstractLogWriter)) {
						throw new Exception("Log driver {$writer} must extend AbstractLogWriter");
					}

					$count++;
				}
			}

			register_shutdown_function(function() {
				\Koldy\Log::shutdown();
			});
		}
	}


	/**
	 * Is there any log driver enabled in this moment?
	 * You can also check this by inspecting LOG constant. Example:
	 * 
	 * 		if (LOG) {
	 * 			// log is enabled
	 * 		}
	 * 
	 * @return boolean
	 */
	public static function isEnabled() {
		static::init();
		return (sizeof(static::$writers) > 0);
	}


	/**
	 * Write DEBUG message to log
	 * 
	 * @param string $string
	 * @link http://koldy.net/docs/log#usage
	 */
	public static function debug($string) {
		static::init();

		foreach (static::$writers as $writer) {
			/* @var $writer \Koldy\Log\Writer\AbstractLogWriter */
			$writer->debug($string);
		}
	}


	/**
	 * Write NOTICE message to log
	 * 
	 * @param string $string
	 * @link http://koldy.net/docs/log#usage
	 */
	public static function notice($string) {
		static::init();

		foreach (static::$writers as $writer) {
			/* @var $writer \Koldy\Log\Writer\AbstractLogWriter */
			$writer->notice($string);
		}
	}


	/**
	 * Write INFO message to log
	 * 
	 * @param string $string
	 * @link http://koldy.net/docs/log#usage
	 */
	public static function info($string) {
		static::init();

		foreach (static::$writers as $writer) {
			/* @var $writer \Koldy\Log\Writer\AbstractLogWriter */
			$writer->info($string);
		}
	}


	/**
	 * Write WARNING message to log
	 * 
	 * @param string $string
	 * @link http://koldy.net/docs/log#usage
	 */
	public static function warning($string) {
		static::init();

		foreach (static::$writers as $writer) {
			/* @var $writer \Koldy\Log\Writer\AbstractLogWriter */
			$writer->warning($string);
		}
	}


	/**
	 * Write ERROR message to log
	 * 
	 * @param string $string
	 * @link http://koldy.net/docs/log#usage
	 */
	public static function error($string) {
		static::init();

		foreach (static::$writers as $writer) {
			/* @var $writer \Koldy\Log\Writer\AbstractLogWriter */
			$writer->error($string);
		}
	}


	/**
	 * Write SQL query to log
	 * 
	 * @param string $sql
	 * @link http://koldy.net/docs/log#usage
	 */
	public static function sql($sql) {
		static::init();

		foreach (static::$writers as $writer) {
			/* @var $writer \Koldy\Log\Writer\AbstractLogWriter */
			$writer->sql($sql);
		}
	}


	/**
	 * Write EXCEPTION message to log
	 * 
	 * @param \Exception $e
	 * @link http://koldy.net/docs/log#usage
	 */
	public static function exception(\Exception $e) {
		static::init();

		foreach (static::$writers as $writer) {
			/* @var $writer \Koldy\Log\Writer\AbstractLogWriter */
			$writer->exception($e);
		}
	}


	/**
	 * This method is called internally on request shutdown event. Do not use
	 * it on your own!
	 */
	public static function shutdown() {
		if (static::isEnabled()) {
			foreach (static::$writers as $writer) {
				/* @var $writer \Koldy\Log\Writer\AbstractLogWriter */
				$writer->shutdown();
			}
		}
	}

}
