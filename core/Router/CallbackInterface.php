<?php
/**
 * Callback Interface
 *
 * @package Coordinator\Engine\Router
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Router;

interface CallbackInterface{

	public function __construct(
	 string $controller,
	 string $method
	);

	public function getController():string;
	public function getMethod():string;

	public function debug():array;

}