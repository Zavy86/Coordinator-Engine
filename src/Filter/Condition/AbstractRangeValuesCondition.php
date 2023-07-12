<?php
/**
 * Abstract Range Values Condition
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter\Condition;

abstract class AbstractRangeValuesCondition extends Condition{
	public function __construct(string $property,int|float|string $value_from,int|float|string $value_to){
		parent::__construct($property,[$value_from,$value_to]);
	}
}
