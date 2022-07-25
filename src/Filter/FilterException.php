<?php
/**
 * Filter Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter;

class FilterException extends \Exception{

	public static function conditionsOperatorInvalid(string $operator):static{
		return new static("Condition operator ".$operator." is not valid. Supported operator are: AND, OR");
	}

}