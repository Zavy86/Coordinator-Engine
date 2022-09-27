<?php
/**
 * Model Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Model;

final class ModelException extends \Exception{

	public static function uidNull():static{
		return new static("UID cannot be null.");
	}

	public static function propertyNotExists(string $class,string $property):static{
		return new static('Property `'.$property.'` does not exists in model `'.$class.'`.');
	}

}
