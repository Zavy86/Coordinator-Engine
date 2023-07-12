<?php
/**
 * Abstract Null Value Condition
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter\Condition;

abstract class AbstractNullValueCondition extends AbstractCondition{
	public function __construct(string $property){
		parent::__construct($property,null);
	}
}
