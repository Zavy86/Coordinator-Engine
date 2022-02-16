<?php
/**
 * Utilities Controller
 *
 * @package Coordinator\Engine\Utilities
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoints\Utilities;

use Coordinator\Engine\Controller\AbstractController;

final class Controller extends AbstractController{

	public function GET_ping(){
		$this->Response->setObject(new PingResponse());
	}

	public function GET_time(){
		$this->Response->setObject(new TimeResponse());
	}

}