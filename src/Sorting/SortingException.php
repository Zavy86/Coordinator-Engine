<?php
/**
 * Sorting Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Sorting;

class SortingException extends \Exception{

	public static function parsingError():static{
		return new static("Error parsing sorting");
	}

	public static function methodInvalid(string $method):static{
		return new static("Sorting method ".$method." is not valid. Supported method are: ASC, DESC");
	}

}
