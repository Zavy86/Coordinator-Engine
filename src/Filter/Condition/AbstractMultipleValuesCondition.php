<?php
/**
 * Abstract Multiple Values Condition
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter\Condition;

abstract class AbstractMultipleValuesCondition extends Condition{
	public function __construct(string $property,array $value){
		parent::__construct($property,$value);
	}
}
