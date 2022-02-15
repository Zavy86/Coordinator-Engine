<?php
/**
 * Authentication Router
 *
 * @package Coordinator\Engine\Endpoints\Authentication
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoints\Authentication;

use Coordinator\Engine\Router\AbstractRouter;
use Coordinator\Engine\Callback\Callback;

/**
 * Authentication Router
 */
class AuthenticationRouter extends AbstractRouter{

	public function loadRoutes(){
		$this->GET("/^\/Authentication\/Check$/",(new Callback(AuthenticationController::class,"GET_check")));
		$this->POST("/^\/Authentication\/Authenticate$/",(new Callback(AuthenticationController::class,"POST_authenticate")));
	}

}