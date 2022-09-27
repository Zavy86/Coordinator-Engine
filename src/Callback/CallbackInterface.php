<?php
/**
 * Callback Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Callback;

interface CallbackInterface{

	/**
	 * Callback constructor
	 *
	 * @param string $controller
	 * @param string $function
	 */
	public function __construct(
	 string $controller,
	 string $function
	);

	/**
	 * Get Controller name
	 *
	 * @return string
	 */
	public function getController():string;

	/**
	 * Get Function name
	 *
	 * @return string
	 */
	public function getFunction():string;

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}
