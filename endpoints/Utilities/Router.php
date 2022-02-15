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

final class Router extends AbstractRouter{

	public function loadRoutes(){
		$this->GET("/^\/Utilities\/Ping$/",(new Callback(Controller::class,"GET_ping")));
		$this->GET("/^\/Utilities\/Time$/",(new Callback(Controller::class,"GET_time")));
	}

}