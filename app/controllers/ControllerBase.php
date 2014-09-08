<?php
namespace Amplus\Controllers;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller {

	public $config;

	protected function initialize() {
		$this->tag->prependTitle("Amplus | ");

		$this->config = $this->di->get('config');				
		$this->view->baseUrl = $this->config->application->baseUri;
	}

	protected function sendMailAction()  {
	    $email_fields = array(
	                    "subject" => "test",
	                    "toName"  => "Amplus",
	                    "content" => "content"
	                );
		$this->di->get("mail")->send("info@amplusmarketing.com", $email_fields);     	
	}	

}