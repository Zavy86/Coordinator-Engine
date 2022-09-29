<?php
/**
 * Endpoint Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoint;

final class EndpointException extends \Exception{

	public const GENERIC_EXCEPTION=101;
	final public static function genericException(string $exception):static{
		return new static($exception,self::GENERIC_EXCEPTION);
	}

}
