<?php
namespace Amplus\Controllers;

use Amplus\Models\Affiliates;

class IndexController extends ControllerBase
{
	protected function initialize() {
        parent::initialize();
		$this->tag->appendTitle("Dashboard");
	}

    public function indexAction() {
        $this->view->hello = "hi";
    }
}

