<?php
/**
 * Filter Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter;

interface FilterInterface{

	public function __construct(Conditions|Condition $condition);

	public function getCondition():Conditions|Condition;

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}
