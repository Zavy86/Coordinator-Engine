<?php
/**
 * Router Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Router;

final class RouterException extends \Exception{

	public const ROUTE_NOT_RESOLVED=101;
	public static function routeNotResolved(string $class,string $command,string $method):static{
		return new static('Route '.strtoupper($method).' '.$command.' was not resolved in '.$class.'.',static::ROUTE_NOT_RESOLVED);
	}

	public const METHOD_NOT_ALLOWED=201;
	public static function methodNotAllowed(string $class,string $command,string $method):static{
		return new static('Method '.$method.' was not allowed in '.$command.' of '.$class.'.',static::METHOD_NOT_ALLOWED);
	}

}