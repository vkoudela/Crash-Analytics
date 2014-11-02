<?php
/**
 * Config(s) for sending mails. The first block will always be default for 
 * sending mail. So, if you want to send mail using default config, use:
 * 
 * 		Mail::create()
 *   		->to('your@mail.com')
 *     		->from('server@mail.com')
 *       	->subject('Mail subject')
 *        	->body('Your mail body')
 *         	->send();
 * 
 * If you want to send mail using "backup" config, then:
 * 
 * 		Mail::create('backup')
 *   		->...
 *         	->send();
 * 
 * @link http://koldy.net/docs/mail
 */
return array(


	/**
	 * Driver for using internal mail() function
	 * 
	 * @link http://koldy.net/docs/mail/mail
	 */
	'default' => array(
		'enabled' => true,
		'driver_class' => '\Koldy\Mail\Driver\Mail'
	),


	/**
	 * PHPMailer configuration
	 * 
	 * @link http://koldy.net/docs/mail/phpmailer
	 */
	'phpmailer' => array(
		'enabled' => true,
		'driver_class' => '\Koldy\Mail\Driver\PHPMailer',

		'options' => array(
			'type' => 'smtp', // smtp|mail
			'host' => 'localhost',
			'port' => 25,
			'username' => null,
			'password' => null,
			'secure' => false
		)
	),


	/**
	 * This won't acutally send e-mail. It will just simulate sending by
	 * printing log message. This is good in development environment.
	 * 
	 * @link http://koldy.net/docs/mail/simulate
	 */
	'simulate' => array(
		'enabled' => true,
		'driver_class' => '\Koldy\Mail\Driver\Simulate'
	)

);
