<?php
/**
 * Handler Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Handler;

final class HandlerException extends \Exception{

	public const ERROR_LOADING_ENDPOINT=101;
	public static function errorLoadingEndpoint(string $endpoint):static{
		return new static('An error occurred while loading '.$endpoint.' endpoint.',static::ERROR_LOADING_ENDPOINT);
	}

	public const ERROR_LOADING_ROUTER=201;
	public static function errorLoadingRouter(string $endpoint):static{
		return new static('An error occurred while loading endpoint '.$endpoint.' router.',static::ERROR_LOADING_ROUTER);
	}

	public const ERROR_LOADING_CALLBACK=301;
	public static function errorLoadingCallback(string $method,string $command):static{
		return new static('An error occurred while loading callback '.strtoupper($method).':'.$command.'.',static::ERROR_LOADING_CALLBACK);
	}

	public const ERROR_LOADING_CONTROLLER=401;
	public static function errorLoadingController(string $endpoint,string $controller):static{
		return new static('An error occurred while loading controller '.$controller.' of endpoint '.$endpoint.'.',static::ERROR_LOADING_CONTROLLER);
	}

	public const FUNCTION_NOT_IMPLEMENTED=501;
	public static function functionNotImplemented(string $endpoint,string $controller,string $method):static{
		return new static('Method '.$method.' was not implemented in controller '.$controller.' of endpoint '.$endpoint.'.',static::FUNCTION_NOT_IMPLEMENTED);
	}

	public const METHOD_NOT_ALLOWED=601;
	public static function methodNotAllowed(string $method,string $command):static{
		return new static('Method '.$method.' was not allowed in '.$command.'.',static::METHOD_NOT_ALLOWED);
	}

}