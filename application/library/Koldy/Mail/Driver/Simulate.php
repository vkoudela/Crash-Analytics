<?php namespace Koldy\Mail\Driver;

use Koldy\Log;

/**
 * This is mail driver class that will simulate sending the e-mail. Instead of
 * actually sending the mail, this class will dump email data into log [INFO].
 * 
 * @link http://koldy.net/docs/mail/simulate
 */
class Simulate extends AbstractDriver {


	/**
	 * The array of recipients
	 * 
	 * @var array
	 */
	private $to = array();


	/**
	 * @var string
	 */
	private $fromEmail = null;


	/**
	 * @var string
	 */
	private $fromName = null;


	/**
	 * @var string
	 */
	private $subject = null;


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::from()
	 */
	public function from($email, $name = null) {
		$this->fromEmail = $email;
		$this->fromName = $name;
		return $this;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::to()
	 */
	public function to($email, $name = null) {
		$this->to[] = array(
			'email' => $email,
			'name' => $name
		);
		return $this;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::subject()
	 */
	public function subject($subject) {
		$this->subject = $subject;
		return $this;
	}


	/**
	 * Sets the e-mail's body in HTML format. If you want to send plain text only, please use plain() method.
	 * 
	 * @param string $body
	 * @param boolean $isHTML
	 * @param string $alternativeText
	 * @return \Koldy\Mail\Driver\Simulate
	 */
	public function body($body, $isHTML = false, $alternativeText = null) {
		return $this;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::attachFile()
	 */
	public function attachFile($filePath, $name = null) {
		return $this;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::header()
	 */
	public function header($name, $value) {
		return $this;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::send()
	 */
	public function send() {
		$from = ($this->fromName !== null) ? "{$this->fromName} <{$this->fromEmail}>" : $this->fromEmail;

		$to = '';

		foreach ($this->to as $toUser) {
			$to .= ($toUser['name'] !== null) ? "{$toUser['name']} <{$toUser['email']}>" : $toUser['email'];
			$to .= ', ';
		}

		$to = substr($to, 0, -2);

		Log::info("E-mail [SIMULATED] is sent from {$from} to {$to} with subject: {$this->subject}");
		return true;
	}

}
