<?php
/**
 * This is application's main config.
 */
return array(

	/**
	 * The site URL WITHOUT ending slash.
	 *
	 * If this is array, then framework will assume that you want to run this site
	 * on multiple domains and thats cool, but there always has to be one primary
	 * domain. So, if this is array, then the first defined domain will be
	 * used as the main domain. This is the case if you run anything in CLI
	 * environment - then domain can't be detected so the first one will be used.
	 *
	 * @var string|array
	 * @example http://your-domain.com
	 * @example array('http://your-domain.com', 'http://second-domain.com', 'http://something.your-domain.com')
	 * @example array('http://your-domain.dev', 'http://your-domain.com')
	 */
	'site_url' => null,


	/**
	 * The CDN URL for resources - WITHOUT ending slash.
	 *
	 * Set null if you don't have any. I'm recommending that you always generate
	 * your URLs to assets with Url::cdn() method even if you don't have any
	 * CDN or separate domain for your assets. It won't hurt you, but one day,
	 * if you get the separate server, then just update this config and voila!
	 *
	 * @var string
	 * @example http://cdn.your-domain.com
	 * @example http://static.your-domain.com/assets
	 */
	'cdn_url' => null,


	/**
	 * The application environment. Sometimes, developers might want to do specific
	 * flows in development and in production environment. Change that flag here.
	 * 
	 * Framework by default looks for this option to know weather to print the
	 * exception stacktrace or not. If in production, then stacktrace will never
	 * be printed out to users. If in development, then framework assumes that
	 * you're running this locally and it will give you the stacktrace immediately
	 * so you can debug/troubleshoot right away.
	 * 
	 * @var string
	 * @example DEVELOPMENT or PRODUCTION
	 */
	'env' => 'DEVELOPMENT',


	/**
	 * The default timezone. This will be used for date_default_timezone_set() function.
	 * You MUST set this! By default, we use UTC everywhere and I'm recommending
	 * UTC for you too.
	 * 
	 * @var string
	 * @example UTC, Europe/Zagreb
	 * @link http://www.php.net/date_default_timezone_set
	 */
	'timezone' => 'UTC',


	/**
	 * The full path to application folder WITH ending slash. If null, then it
	 * will be autodetected from path you pass to useConfig() method in index.php
	 * 
	 * @var string
	 */
	'application_path' => null,


	/**
	 * The full path to public_html folder WITH ending slash. If null,
	 * then it will be autodetected from SCRIPT_FILENAME
	 * 
	 * @var string
	 */
	'public_path' => null,


	/**
	 * The full path to storage folder that shouldn't be accessible via HTTP - WITH ending slash.
	 * If null, then it will be autodetected by assuming it is in the same
	 * directory level as application_path
	 * 
	 * @var string
	 */
	'storage_path' => null,


	/**
	 * If you need to register additional include paths for your application,
	 * then define this array and fill it with the include paths you need
	 * 
	 * @var array
	 * @example array('/var/lib/3rdparty/something', '/home/my/libs/PHPMailer')
	 */
	'additional_include_path' => null,


	/**
	 * If you have modules that you want to be able to access on every request
	 * and you don't want to register them manually with Application::registerModule(),
	 * then please define those modules here. All you need is to set the array
	 * of the modules you want.
	 * 
	 * @var array
	 * @example array('users','news')
	 */
	'auto_register_modules' => null,


	/**
	 * Random key, 32 chars max. It will be used as site identifier and for
	 * building any kind of specific keys for this site. Useful in situations when
	 * more sites are accessing same Memcache storage, or something like that.
	 * 
	 * @var string
	 */
	'key' => '_____ENTERSomeRandomKeyHere_____',


	/**
	 * The routing class to parse URIs and to generate links to resources and other pages
	 * 
	 * @var string
	 * @link http://koldy.net/docs/routes
	 */
	'routing_class' => '\Koldy\Application\Route\DefaultRoute',


	/**
	 * The additional routing options that will be passed to route constructor. Check
	 * the documentation for details.
	 * 
	 * @var array
	 * @link http://koldy.net/docs/routes
	 */
	'routing_options' => array(
		'always_restful' => false
	),


	/**
	 * The log config. You can define more then one log writer and all of them will
	 * be used. By default, the first one is enabled only for standard HTTP requests,
	 * and the second one is used only when script is called in CLI environment.
	 * 
	 * @var array
	 * @link http://koldy.net/docs/log
	 */
	'log' => array(

		/**
		 * The default writer to FILE - by default enabled only for regular HTTP requests
		 * 
		 * @link http://koldy.net/docs/log/file
		 */
		array(
			'enabled' => (PHP_SAPI != 'cli'),
			'writer_class' => '\Koldy\Log\Writer\File',
			'options' => array(
				'path' => null,
				'log' => array('debug', 'notice', 'info', 'warning', 'error', 'sql', 'exception'),
				'email_on' => array('warning', 'error', 'exception'),
				'email' => 'your@email.com',
				'dump' => array()
			)
		),

		/**
		 * Writer to console - prepared to work only in CLI environment
		 * 
		 * @link http://koldy.net/docs/log/out
		 */
		array(
			'enabled' => (PHP_SAPI == 'cli'),
			'writer_class' => '\Koldy\Log\Writer\Out',
			'options' => array(
				'log' => array('debug', 'notice', 'info', 'warning', 'error', 'sql', 'exception'),
				'email_on' => array('warning', 'error', 'exception'),
				'email' => 'your@email.com',
				'dump' => array('speed')
			)
		),

		/**
		 * Logging into database - disabled by default, but its here just for an example if you want to use it
		 * 
		 * @link http://koldy.net/docs/log/db
		 */
		array(
			'enabled' => false,
			'writer_class' => '\Koldy\Log\Writer\Db',
			'options' => array(
				'log' => array('debug', 'notice', 'info', 'warning', 'error', 'sql', 'exception'),
				'email_on' => array('warning', 'error', 'exception'),
				'email' => 'your@email.com',
				'connection' => null,
				'table' => 'log'
			)
		)

	),


	/**
	 * The common shorthand aliases for classes. If needed, you can override those
	 * with the ones you need.
	 * 
	 * @var array
	 * @link http://www.php.net/manual/en/function.class-alias.php
	 */
	'classes' => array(
		'App'			=> '\\Koldy\\Application',
		'Application'	=> '\\Koldy\\Application',
		'Cache'			=> '\\Koldy\\Cache',
		'Cli'			=> '\\Koldy\\Cli',
		'Convert'		=> '\\Koldy\\Convert',
		'Cookie'		=> '\\Koldy\\Cookie',
		'Crypt'			=> '\\Koldy\\Crypt',
		'Db'			=> '\\Koldy\\Db',
		'Directory'		=> '\\Koldy\\Directory',
		'Download'		=> '\\Koldy\\Download',
		'Html'			=> '\\Koldy\\Html',
		'HttpRequest'	=> '\\Koldy\\Http\\Request',
		'Input'			=> '\\Koldy\\Input',
		'Json'			=> '\\Koldy\\Json',
		'Log'			=> '\\Koldy\\Log',
		'Mail'			=> '\\Koldy\\Mail',
		'Pagination'	=> '\\Koldy\\Pagination',
		'Redirect'		=> '\\Koldy\\Redirect',
		'Request'		=> '\\Koldy\\Request',
		'Response'		=> '\\Koldy\\Response',
		'Server'		=> '\\Koldy\\Server',
		'Session'		=> '\\Koldy\\Session',
		'Timezone'		=> '\\Koldy\\Timezone',
		'Url'			=> '\\Koldy\\Url',
		'Validator'		=> '\\Koldy\\Validator',
		'View'			=> '\\Koldy\\View',
		'Where'			=> '\\Koldy\\Db\\Where'
	),


	/**
	 * You custom error handler for all PHP errors, including E_ERROR (Fatal Errors)
	 * 
	 * @link http://php.net/manual/en/function.set-error-handler.php
	 */
// 	'error_handler' => function($errno, $errstr, $errfile, $errline) {
// 		// Do something, but be careful. Remember that E_ERROR is not recoverable.
// 	}

);
