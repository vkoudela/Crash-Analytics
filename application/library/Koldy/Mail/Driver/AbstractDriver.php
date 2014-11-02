<?php namespace Koldy\Mail\Driver;

/**
 * If you want to create your own driver for sending e-mails, then please
 * extend this class. Thank you!
 *
 */
abstract class AbstractDriver {


	/**
	 * The config array passed from config/mail.php; the 'options' key
	 * 
	 * @var array
	 */
	protected $config;


	/**
	 * If error occured, then last message is set here
	 * 
	 * @var string
	 */
	protected $lastError = null;


	/**
	 * If exception occured, then last exception instance is stored here
	 * 
	 * @var \Exception
	 */
	protected $lastException = null;


	/**
	 * Construct the object with configuration array from config/mail.php, from 'options' key
	 * 
	 * @param array $config
	 */
	public function __construct(array $config){
		$this->config = $config;
	}


	/**
	 * Set From
	 * 
	 * @param string $email
	 * @param string $name [optional]
	 * @return \Koldy\Mail\Driver\AbstractDriver
	 * @link http://koldy.net/docs/mail#example
	 */
	abstract public function from($email, $name = null);


	/**
	 * Send mail to this e-mail
	 * 
	 * @param string $email
	 * @param string $name [optional]
	 * @return \Koldy\Mail\Driver\AbstractDriver
	 * @link http://koldy.net/docs/mail#example
	 */
	abstract public function to($email, $name = null);


	/**
	 * Set the e-mail subject
	 * 
	 * @param string $subject
	 * @return \Koldy\Mail\Driver\AbstractDriver
	 * @link http://koldy.net/docs/mail#example
	 */
	abstract public function subject($subject);


	/**
	 * Set e-mail body
	 * 
	 * @param string $body
	 * @param boolean $isHTML
	 * @param string $alternativeText The plain text
	 * @return \Koldy\Mail\Driver\AbstractDriver
	 * @link http://koldy.net/docs/mail#example
	 */
	abstract public function body($body, $isHTML = false, $alternativeText = null);


	/**
	 * Attach file into this e-mail
	 * 
	 * @param string $filePath
	 * @param string $name
	 * @return \Koldy\Mail\Driver\AbstractDriver
	 * @link http://koldy.net/docs/mail#header-and-files
	 */
	abstract public function attachFile($filePath, $name = null);


	/**
	 * Sends an e-mail
	 * 
	 * @return boolean
	 * @link http://koldy.net/docs/mail#example
	 */
	abstract public function send();


	/**
	 * Set the custom mail header
	 * 
	 * @param string $name
	 * @param string $value
	 * @link http://koldy.net/docs/mail#header-and-files
	 */
	abstract public function header($name, $value);


	/**
	 * Set last error message that occured
	 * 
	 * @param string $message
	 * @return \Koldy\Mail\Driver\AbstractDriver
	 */
	protected function setErrorMessage($message) {
		$this->lastError = $message;
		return $this;
	}


	/**
	 * Set last error exception
	 * 
	 * @param \Exception $e
	 * @return \Koldy\Mail\Driver\AbstractDriver
	 */
	protected function setErrorException(\Exception $e) {
		$this->lastException = $e;
		return $this;
	}


	/**
	 * Is error string set or not
	 * 
	 * @return boolean
	 * @link http://koldy.net/docs/mail#example
	 */
	public function hasError() {
		return $this->lastError !== null;
	}


	/**
	 * Get the error message as string
	 * 
	 * @return string
	 * @link http://koldy.net/docs/mail#example
	 */
	public function getError() {
		return $this->lastError;
	}


	/**
	 * Is error exception set or not
	 * 
	 * @return boolean
	 * @link http://koldy.net/docs/mail#example
	 */
	public function hasException() {
		return $this->lastException !== null;
	}


	/**
	 * Get the error exception if there is any
	 * 
	 * @return Exception or null
	 * @link http://koldy.net/docs/mail#example
	 */
	public function getException() {
		return $this->lastException;
	}


	/**
	 * Is mail sending mailed or not
	 * 
	 * @return boolean
	 * @link http://koldy.net/docs/mail#example
	 */
	public function hasFailed() {
		return $this->hasException() || $this->hasError();
	}

}
