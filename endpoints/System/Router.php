<?php
/**
 * System Router
 *
 * @package Coordinator\Engine\System
 * @author Manuel Zavatta <manuel.zavatta@cogne.com>
 */

namespace Coordinator\Engine\Endpoints\System;

use Coordinator\Engine\Router\AbstractRouter;
use Coordinator\Engine\Callback\Callback;

class Router extends AbstractRouter{

	public function loadRoutes(){

		$this->GET("/^\/System\/Accounts$/",(new Callback(Account\Controller::class,"browse")));
		$this->GET("/^\/System\/Accounts\/[a-fA-F0-9]+$/",(new Callback(Account\Controller::class,"load")));
		$this->POST("/^\/System\/Accounts$/",(new Callback(Account\Controller::class,"create")));
		$this->PUT("/^\/System\/Accounts\/[a-fA-F0-9]+$/",(new Callback(Account\Controller::class,"update")));
		$this->DELETE("/^\/System\/Accounts\/[a-fA-F0-9]+$/",(new Callback(Account\Controller::class,"remove")));

		$this->GET("/^\/System\/Tokens$/",(new Callback(Token\Controller::class,"browse")));
		$this->GET("/^\/System\/Tokens\/[a-fA-F0-9]+$/",(new Callback(Token\Controller::class,"load")));
		$this->POST("/^\/System\/Tokens$/",(new Callback(Token\Controller::class,"create")));
		$this->PUT("/^\/System\/Tokens\/[a-fA-F0-9]+$/",(new Callback(Token\Controller::class,"update")));
		$this->DELETE("/^\/System\/Tokens\/[a-fA-F0-9]+$/",(new Callback(Token\Controller::class,"remove")));

	}

}