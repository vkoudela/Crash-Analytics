<?php

/**
 * Initially, the index.php will work automatically if you place Koldy Framework
 * in [your-project]/application/library/
 */


/**
 * Always, but *always* include the Application.php by including the full path
 * on the file system! If you know only the relative path, then resolve the
 * full path with realpath() function.
 */
require realpath(dirname(__FILE__) . '/../application/library/Koldy/Application.php');


/**
 * The parameter in "useConfig" can be defined relative to this index.php
 */
Koldy\Application::useConfig('../application/configs/application.php');


/**
 * And now, just run it!
 * 
 * If you want to temorary close the website, then you can pass the URI
 * to run() method like: Koldy\Application::run('/temporary/closed'); and
 * insted of using real REQUEST_URI, framework will use '/temporary/closed'
 */
Koldy\Application::run();
