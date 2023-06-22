<?php
/**
 * Filter Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter;

class FilterException extends \Exception{

	public static function parsingError():static{
		return new static("Error parsing filter");
	}

	public static function conditionInvalid(string $assertion):static{
		return new static("Condition ".$assertion." is not valid. Supported assertions are: isNull, isNotNull, isEqualsTo, isNotEqualsTo, isGreaterThan, isGreaterEqualThan, isLesserThan, isLesserEqualThan, isLike, isNotLike, isIn, isNotIn, isBetween, isNotBetween");
	}

	public static function conditionsOperatorInvalid(string $operator):static{
		return new static("Condition operator ".$operator." is not valid. Supported operator are: AND, OR");
	}

}
