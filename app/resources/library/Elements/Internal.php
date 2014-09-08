<?php
namespace Amplus\Elements;
use Phalcon\Mvc\User\Component;

class Internal extends Component {
	/**
	  * Builds header menu with left and right items
	  *
	  * @return string
	  */
	public function getMenu() {
		echo "<header>
				<div class='logo'></div>
			  </header>";
	}
}