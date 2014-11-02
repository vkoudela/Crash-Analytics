<?php

return array(
	/**
	 * The first defined cache driver is the default one. This one will be used
	 * if you don't specify this key when calling Cache::driver()
	 * 
	 * @link http://koldy.net/docs/cache/files#configuration
	 */
	'files' => array(
		'enabled' => true,
		'driver_class' => '\Koldy\Cache\Driver\Files',

		'options' => array(
			'path' => null, // if null, then path is [storage]/cache
			'default_duration' => 3600
		)
	),


	/**
	 * This is example of some other cache driver that will store cached files
	 * on some other location. Call this by using Cache::driver('tmp')
	 * 
	 * @link http://koldy.net/docs/cache/files#configuration
	 */
	'tmp' => array(
		'enabled' => true,
		'driver_class' => '\Koldy\Cache\Driver\Files',

		'options' => array(
			'path' => '/full/path/to/storage/tmp/',
			'default_duration' => 10
		)
	),


	/**
	 * If you don't want to disable cache driver, but you still want to use it in
	 * some simulations or simulated environment, then use DevNull type of driver.
	 * If you want to make this one default, then move this key on the top of this
	 * file or call this driver with Cache::driver('nowhere')
	 * 
	 * @link http://koldy.net/docs/cache/devnull
	 */
	'nowhere' => array(
		'enabled' => true,
		'driver_class' => '\Koldy\Cache\Driver\DevNull'
	),


	/**
	 * If you want to cache your data into table in database, then use this cache
	 * driver.
	 * 
	 * @link http://koldy.net/docs/cache/db
	 */
	'db' => array(
		'enabled' => true,
		'driver_class' => '\Koldy\Cache\Driver\Db',

		'options' => array(
			'connection' => null,
			'table' => 'cache',
			'default_duration' => 3600,
			'clean_old' => (rand(1, 100) % 100 == 0) // the 1:100 probability to clean old items
		)
	)

);
