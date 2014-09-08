<?php
namespace Amplus\Plugins;

use Phalcon\Events\Event,
    Phalcon\Mvc\User\Plugin,
    Phalcon\Mvc\Dispatcher,
    Phalcon\Acl\Adapter\Memory,
    Phalcon\Acl\Resource,
    Phalcon\Acl;

/**
 * Security
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
class Security extends Plugin
{

    public function __construct($dependencyInjector)
    {
        $this->_dependencyInjector = $dependencyInjector;
    }

    public function _getAcl()
    {
        if (!isset($this->persistent->acl)) {

            $acl = new Memory();

            $acl->setDefaultAction(Acl::ALLOW);

            //Register roles
            $roles = array(
                'admin' => new Acl\Role('Administrator'),
                'manager' => new Acl\Role('Manager'),
                'staff' => new Acl\Role('Staff')
            );
            
            $acl->addRole($roles['staff']);
            $acl->addRole($roles['manager']);
            $acl->addRole($roles['admin']); // admin inherits staff


            // resources that sales are denied
            $staffResources = array(
                "reports" => array("index")
            );

            // add resources for sales
            foreach($staffResources as $resource => $actions) {
                $acl->addResource(new Resource($resource), $actions);
                foreach($actions as $action) {
                    $acl->deny($roles['staff']->getName(), $resource, $action);
                }
                // $acl->allow($roles['staff']->getName(), $resource, '*');
            }
            
            //The acl is stored in session, APC would be useful here too
            $this->persistent->acl = $acl;
        }

        return $this->persistent->acl;
    }

    /**
     * This action is executed before execute any action in the application
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {

        $auth = $this->session->get('auth');
        if (!$auth){
            $role = 'Guest';
        } else {
            $role = $auth['accountType'];
        }

        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        $acl = $this->_getAcl();

        $allowed = $acl->isAllowed($role, $controller, $action);
        if ($allowed != Acl::ALLOW) {
            $this->flashSession->error("You don't have access to this module: $controller/$action");
            // $this->flashSession->error("$role $controller $action");    
            $dispatcher->forward(
                array(
                    'controller' => 'dashboard',
                    'action' => 'index'
                )
            );

            return false;
        }
        return;
    }

}