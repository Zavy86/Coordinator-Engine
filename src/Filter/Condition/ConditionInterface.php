<?php
/**
 * Condition Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter\Condition;

interface ConditionInterface{

	public function getAssertion():string;
	public function getProperty():string;
	public function getValue():mixed;

	public function returnTextual(int $depth=0):string;

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}
