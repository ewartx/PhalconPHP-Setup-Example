<?php
namespace Amplus\Models;

use Phalcon\Mvc\Model;

class ModelBase extends Model {

	public function notSave() {
	    //Obtain the flash service from the DI container
	    $flash = $this->getDI()->getFlash();

	    //Show validation messages
	    foreach ($this->getMessages() as $message) {
	        $flash->error($message);
	    }
	}	
}