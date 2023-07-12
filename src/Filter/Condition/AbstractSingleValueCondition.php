<?php
/**
 * Abstract Single Value Condition
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter\Condition;

abstract class AbstractSingleValueCondition extends AbstractCondition{
	public function __construct(string $property,bool|int|float|string $value){
		parent::__construct($property,$value);
	}
}
