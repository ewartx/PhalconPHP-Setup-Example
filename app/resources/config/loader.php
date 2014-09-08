<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */

$loader->registerNamespaces(array(       
    	"Amplus\Controllers"  => $config->application->controllersDir,    	 
    	"Amplus\Models" => $config->application->modelsDir,
    	"Amplus\Plugins"    => $config->application->pluginsDir,  
    	"Amplus"    => $config->application->libraryDir   
));

$loader->register();

require_once __DIR__ . '/../../../composer_libraries/autoload.php';