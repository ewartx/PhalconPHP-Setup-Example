<?php
use Phalcon\Config;

$isLocal = (in_array($_SERVER["SERVER_ADDR"], array("127.0.0.1","::1")) || strpos($_SERVER["SERVER_ADDR"],'192.168') !== false) ? true : false;
$isProduction = ($_SERVER['HTTP_HOST'] == 'production.amplusmarketing.com') ? true : false;

// Databases
$remoteDB = array(
			'adapter'  => 'Mysql',
			'host'     => '',
			'username' => '',
			'password' => '',
			'name'     => ''
		);

$localDB = array(
			'adapter'  => 'Mysql',
			'host'     => 'localhost',
			'username' => 'root',
			'password' => '',
			'name'     => ''
		);

// Paths
$applicationPaths = array(
							'controllersDir' => APP_DIR . '/controllers/',
							'modelsDir'      => APP_DIR . '/models/',
							'viewsDir'       => APP_DIR . '/views/',
							'pluginsDir'	 => APP_DIR . '/resources/plugins/',
							'libraryDir'     => APP_DIR . '/resources/library/',
							'cacheDir'       => APP_DIR . '/cache/'
					);

$localPath = $applicationPaths;
$localPath['baseUri'] = "/phalcon-example/";

$remotePath = $applicationPaths;
$remotePath['baseUri'] = "/";

// mail
$mail = array(
			"fromName"  => "System Admin",
			"fromEmail" => "webmaster@amplusmarketing.com",
			"smtp" => array(
				'server' => 'smtp.gmail.com',
				'port'   => 587,
				'security' => 'tls',
				'username' => '',
				'password' => '',
				'debug'    => false
			)
		);

/****** 3 Cases *******/

// Case 1: Working Locally
if ($isLocal) {
	return new Config(array(
		'database' => $localDB,
		'application' => $localPath,
		'mail' => $mail
	));
}

// Case 3: Remote, Production Site
if($isProduction) {
	return new Config(array(
		'database' => $remoteDB,		
		'application' => $remotePath,
		'mail' => $mail
	));
}