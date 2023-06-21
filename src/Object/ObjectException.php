<?php
/**
 * Object Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Object;

final class ObjectException extends \Exception{

	final public static function genericException(string $exception):static{
		return new static($exception);
	}

	public static function propertyNotExists(string $class,string $property):static{
		return new static('Property `'.$property.'` does not exists in model `'.$class.'`.');
	}

}
