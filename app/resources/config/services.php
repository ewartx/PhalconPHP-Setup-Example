<?php

use Phalcon\DI\FactoryDefault,
	Phalcon\Mvc\View,
    Phalcon\Mvc\Dispatcher,
	Phalcon\Mvc\Url as UrlResolver,
	Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
	Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter,
	Phalcon\Mvc\View\Engine\Volt as VoltEngine,    
	Phalcon\Session\Adapter\Files as SessionAdapter;

use Amplus\Mail\Mail,
    Amplus\Elements;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);


/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
    	'.volt' => function($view, $di) use ($config) {
    		$volt = new VoltEngine($view, $di);
    		$volt->setOptions(array(
    			'compiledPath' => $config->application->cacheDir,
    			'compiledSeparator' => '_',
                'compileAlways' => true
    		));
    		return $volt;
    	},
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
}, true);

// Passing on the config to dipatcher so we can access this information later
$di->set('config', $config);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    return new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->name,
        "charset" => 'utf8',
        'dialectClass' => '\Amplus\Plugins\MysqlExtended'
    ));
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});


/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter(array(
    				"uniqueId" => "amplus"
    			));
    $session->start();

    return $session;
});

$di->set('router', function(){      
    $router = new \Phalcon\Mvc\Router();
    
    $router
        ->add('/:controller/:action/:params', array(
        'controller' => 1,
        'action' => 2,
        'params' => 3
    ))
        ->convert('action', function($action) { //The action name allows dashes
            $actionParts = explode("-", $action);   $partName = "";
            foreach($actionParts as $key => $part) {
                $partName .= ($key == 0) ? $part : ucfirst($part);              
            }
        return $partName;
    }); 

    //Remove trailing slashes automatically
    $router->removeExtraSlashes(true);

    return $router;
});

//Register the flash service with custom CSS classes
$di->set('flash', function(){
    $flash = new \Phalcon\Flash\Direct(array(
        'error' => 'alert alert-error',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
    ));
    return $flash;
});

//Register an user component
$di->set('internal', function(){
    return new Elements\Internal();
});

/**
 * Mail service uses gmail
 */
$di->set('mail', function () {
    return new Mail();
});

/**
 * Dispatcher use a default namespace
 */
$di->set('dispatcher', function () {
    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('Amplus\Controllers');
    return $dispatcher;
});


/**
*	Set the dispatcher to be available for use and enable Security plugin
**/
// $di->set('dispatcher', function() use ($di) {
//     //Obtain the standard eventsManager from the DI
//     $eventsManager = $di->getShared('eventsManager');

//     $eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception) {
//             switch ($exception->getCode()) {
//                 case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
//                 case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
//                     $dispatcher->forward(
//                         array(
//                             'controller' => 'index',
//                             'action'     => 'show404',
//                         )
//                     );
//                     return false;
//             }
//         }
//     );

//     // //Instantiate the Security plugin
//     $security = new \Amplus\Plugins\Security($di);

//     // //Listen for events produced in the dispatcher using the Security plugin
//     $eventsManager->attach('dispatch', $security);

//     $dispatcher = new Dispatcher();

//     //Bind the EventsManager to the Dispatcher
//     $dispatcher->setEventsManager($eventsManager);

//     return $dispatcher;
// });