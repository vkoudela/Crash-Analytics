<?php namespace Koldy\Mail\Driver;

use Koldy\Exception;

/**
 * This is only driver class that uses PHPMailer. You need to set the include
 * path the way that PHP can include it. We recommend that you set that path
 * in config/application.php under additional_include_path. Path defined there
 * must be the path where class.phpmailer.php is located.
 * 
 * @link http://koldy.net/docs/mail/phpmailer
 */
class PHPMailer extends AbstractDriver {


	/**
	 * @var \PHPMailer
	 */
	private $mailer = null;


	/**
	 * Construct the object
	 * 
	 * @param array $config
	 */
	public function __construct(array $config) {
		parent::__construct($config);

		if (!class_exists('PHPMailer', false)) {

			if (($path = stream_resolve_include_path('class.phpmailer.php')) !== false) {
				require_once $path;

			} else if (($path = stream_resolve_include_path('PHPMailer/class.phpmailer.php')) !== false) {
				require_once $path;

			}

			if (!class_exists('PHPMailer', false)) {
				throw new Exception('PHPMailer class doesn\'t exists or can\'t be found. Please define the include path in config/application.php under additional_include_paths');
			}
		}

		$this->mailer = new \PHPMailer(true);
		$this->mailer->CharSet = isset($config['charset']) ? $config['charset'] : 'UTF-8';
		$this->mailer->Host = $config['host'];
		$this->mailer->Port = $config['port'];

		if (isset($config['username']) && $config['username'] !== null) {
			$this->mailer->Username = $config['username'];
		}

		if (isset($config['password']) && $config['password'] !== null) {
			$this->mailer->Password = $config['password'];
		}

		switch($config['type']) {
			default:
			case 'smtp':
				$this->mailer->IsSMTP();

				if (isset($config['username']) && $config['username'] !== null && isset($config['password']) && $config['password'] !== null) {
					$this->mailer->SMTPAuth = true;
				}
				break;

			case 'mail':
				$this->mailer->IsMail();
				break;
		}
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::from()
	 */
	public function from($email, $name = null) {
		$this->mailer->SetFrom($email, $name);
		return $this;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::to()
	 */
	public function to($email, $name = null) {
		$this->mailer->AddAddress($email, $name === null ? '' : $name);
		return $this;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::subject()
	 */
	public function subject($subject) {
		$this->mailer->Subject = $subject;
		return $this;
	}


	/**
	 * Sets the e-mail's body in HTML format. If you want to send plain text only, please use plain() method.
	 * 
	 * @param string $body
	 */
	public function body($body, $isHTML = false, $alternativeText = null) {
		$this->mailer->Body = $body;

		if ($isHTML) {
			$this->mailer->IsHTML();
		}

		if ($alternativeText !== null) {
			$this->mailer->AltBody = $alternativeText;
		}

		return $this;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::attachFile()
	 */
	public function attachFile($filePath, $name = null) {
		$this->mailer->AddAttachment($filePath, ($name === null ? '' : $name));
		return $this;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::header()
	 */
	public function header($name, $value) {
		$this->mailer->AddCustomHeader($name, $value);
		return $this;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Mail\Driver\AbstractDriver::send()
	 */
	public function send() {
		try {

			if (!$this->mailer->Send()) {
				$this->setErrorMessage($this->mailer->ErrorInfo);
				return false;
			}

			return true;

		} catch (phpmailerException $e) {
			$this->setErrorMessage($e->getMessage());
			$this->setErrorException($e);

		} catch (\Exception $e) {
			$this->setErrorMessage($e->getMessage());
			$this->setErrorException($e);

		}

		return false;
	}


	/**
	 * Get the PHP mailer instance for fine tuning
	 * 
	 * @return \PHPMailer
	 */
	public function getPHPMailer() {
		return $this->mailer;
	}

}
