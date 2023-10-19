<?php
/**
 * Filter Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter;

use Coordinator\Engine\Filter\Condition\ConditionInterface;

interface FilterInterface{

	public function __construct(FilterConditions|ConditionInterface $condition);

	public function getCondition():FilterConditions|ConditionInterface;

	public function getRaw():?array;

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}
