<?php
error_reporting(E_ALL);

date_default_timezone_set("America/Vancouver");

try {

	/**
	 * Read the configuration
	 */
	
	define('BASE_DIR', dirname(__DIR__));
	define('APP_DIR', BASE_DIR . '/app');

	$config = include APP_DIR . "/resources/config/config.php";

	/**
	 * Read auto-loader
	 */
	include APP_DIR . "/resources/config/loader.php";

	/*
	 * Read services
	 */
	include APP_DIR . "/resources/config/services.php";

	/**
	 * Handle the request
	 */
	$application = new \Phalcon\Mvc\Application($di);	
	echo $application->handle()->getContent();

} catch (Exception $e) {
	echo $e->getMessage(), '<br>';
	echo nl2br(htmlentities($e->getTraceAsString()));
}