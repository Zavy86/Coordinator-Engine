<?php
/**
 * Authentication Router
 *
 * @package Coordinator\Engine\Authentication
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoints\Authentication;

use Coordinator\Engine\Router\AbstractRouter;
use Coordinator\Engine\Callback\Callback;

final class Router extends AbstractRouter{

	public function loadRoutes(){
		$this->GET("/^\/Authentication\/Check$/",(new Callback(Controller::class,"GET_check")));
		$this->POST("/^\/Authentication\/Authenticate$/",(new Callback(Controller::class,"POST_authenticate")));
	}

}