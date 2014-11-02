<?php

/**
 * Ignore all requests that will come from web server. We don't need those and
 * act like its nothing there.
 */
if (PHP_SAPI != 'cli') {
	$code = 404;
	$message = 'Page Not Found';
	require '404.php';
	exit(0);
}


/**
 * Define this global constant. This is for framework only, so don't
 * relay on this in the future.
 * @var boolean
 */
define('KOLDY_CLI', true);


/**
 * Continue like this is a normal script
 */
require 'index.php';
