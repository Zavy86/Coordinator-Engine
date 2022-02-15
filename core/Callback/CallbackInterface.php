<?php
/**
 * Callback Interface
 *
 * @package Coordinator\Engine\Callback
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Callback;

interface CallbackInterface{

	public function __construct(
	 string $controller,
	 string $function
	);

	public function getController():string;
	public function getFunction():string;

	public function debug():array;

}