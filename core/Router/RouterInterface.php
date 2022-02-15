<?php
/**
 * Router Interface
 *
 * @package Coordinator\Engine\Router
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Router;

use Coordinator\Engine\Callback\CallbackInterface;

interface RouterInterface{

	public function loadRoutes();

	/**
	 * @throws RouterException
	 */
	public function resolveRoute(string $method,string $command):CallbackInterface;

	public function addRoute(string $method,string $command,CallbackInterface $callback):void;

	public function debug():array;

}