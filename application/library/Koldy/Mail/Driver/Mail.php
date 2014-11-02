<?php namespace Koldy\Mail\Driver;

use Koldy\Exception;
use Koldy\Log;

/**
 * This mail driver class will use just internal mail() function to send
 * an e-mail.
 * 
 * @link http://koldy.net/docs/mail/mail
 */
class Mail extends AbstractDriver {


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
	 * @var string
	 */
	private $body = null;


	/**
	 * @var boolean
	 */
	private $isHTML = null;


	/**
	 * @var array
	 */
	private $headers = array();


	/**
	 * @var string
	 */
	private $alternativeText = null;


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
	 */
	public function body($body, $isHTML = false, $alternativeText = null) {
		$this->body = $body;
		$this->isHTML = $isHTML;
		$this->alternativeText = $alternativeText;

		if ($isHTML) {
			$this->header('MIME-Version', '1.0');
		}

		return $this;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::attachFile()
	 */
	public function attachFile($filePath, $name = null) {
		throw new Exception('AttachFile is not yet implemented in Mail driver');
		return $this;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::header()
	 */
	public function header($name, $value) {
		$this->headers[$name] = $value;
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

		$headers = array(
			'X-Mailer: PHP/' . phpversion(),
		);

		foreach ($this->headers as $name => $value) {
			$headers[] = "{$name}: {$value}";
		}

		$charset = isset($this->config['charset']) ? $this->config['charset'] : 'utf-8';

		$headers[] = ($this->isHTML)
			? ('Content-type: text/html; charset=' . $charset)
			: ('Content-type: text/plain; charset=' . $charset);

		if ($this->fromEmail !== null) {
			$headers[] = "From: {$from}";
		}

		return @mail($to, $this->subject, $this->body, implode("\r\n", $headers));
	}

}
