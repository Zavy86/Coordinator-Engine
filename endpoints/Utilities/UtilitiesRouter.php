<?php
/**
 * Utilities Router
 *
 * @package Coordinator\Engine\Endpoints\Utilities
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoints\Utilities;

use Coordinator\Engine\Router\AbstractRouter;
use Coordinator\Engine\Callback\Callback;

class UtilitiesRouter extends AbstractRouter{

	public function loadRoutes(){
		$this->GET("/^\/Utilities\/Ping$/",(new Callback(UtilitiesController::class,"GET_ping")));
		$this->GET("/^\/Utilities\/Time$/",(new Callback(UtilitiesController::class,"GET_time")));
	}

}