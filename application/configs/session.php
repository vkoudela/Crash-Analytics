<?php
/**
 * The session configuration. As you know, you may have only one session storage
 * and it needs to be defined here.
 * 
 * @link http://koldy.net/docs/session#configuration
 */
return array(

	/*
	'driver_class' => '\Koldy\Session\Driver\Db',
	'options' => array(
		'connection' => null,
		'table' => 'session'
	),
	*/

	'cookie_life' => 0,
	'cookie_path' => '/',
	'cookie_domain' => null,
	'cookie_secure' => false,
	'session_name' => 'koldy'

);
