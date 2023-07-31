<?php
/**
 * Router Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Router;

use Coordinator\Engine\Callback\CallbackInterface;

interface RouterInterface{

	/**
	 * Load Routes
	 */
	public function loadRoutes();

	/**
	 * Resolve Route
	 *
	 * @throws RouterException
	 * @return CallbackInterface
	 */
	public function resolveRoute(string $method,string $command):CallbackInterface;

	/**
	 * Add Route
	 *
	 * @param string $method
	 * @param string $command
	 * @param CallbackInterface $callback
	 */
	public function addRoute(string $method,string $command,CallbackInterface $callback):void;

	/**
	 * Return Routes
	 *
	 * @return CallbackInterface[][]
	 */
	public function getRoutes():array;

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}
