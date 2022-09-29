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

	public static function valueNotAcceptable(string $class,string $property,array $acceptedValues=[]):static{
		$error='The value passed for the `'.$property.'` property is not acceptable ';
		if(count($acceptedValues)){$error.='(possible values: '.implode(', ',$acceptedValues).') ';}
		$error.=' in model `'.$class.'`.';
		return new static( $error);
	}

}
