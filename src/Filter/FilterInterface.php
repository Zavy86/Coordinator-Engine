<?php
/**
 * Filter Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter;

use Coordinator\Engine\Filter\Condition\Conditions;

interface FilterInterface{

	public function __construct(Conditions|ConditionInterface $condition);

	public function getCondition():Conditions|ConditionInterface;

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}
