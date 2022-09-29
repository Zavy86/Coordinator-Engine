<?php
/**
 * Handler Error
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Handler;

use Coordinator\Engine\Error\AbstractError;

final class HandlerError extends AbstractError{  // @todo valutare se spostare sotto Http visto che sono gli errori che escono dall'Handler

	public static function uncatchedError(string $description):static{
		return new static(
			'uncatchedError',     // @todo valutare se mettere handler.uncatchedError
			$description
		);
	}

	public static function errorLoadingEndpoint(string $endpoint):static{
		return new static(
			'errorLoadingEndpoint',
			'An error occurred while loading endpoint '.$endpoint.'.'
		);
	}

	public static function errorLoadingRouter(string $endpoint):static{
		return new static(
			'errorLoadingRouter',
			'An error occurred while loading endpoint '.$endpoint.' router.'
		);
	}

	public static function errorLoadingCallback(string $method,string $command):static{
		return new static(
			'errorLoadingCallback',
			'An error occurred while loading callback '.strtoupper($method).':'.$command.'.'
		);
	}

	public static function errorLoadingController(string $endpoint,string $controller):static{
		return new static(
			'errorLoadingController',
			'An error occurred while loading controller '.$controller.' of endpoint '.$endpoint.'.'
		);
	}

	public static function functionNotImplemented(string $endpoint,string $controller,string $method):static{
		return new static(
			'functionNotImplemented',
			'Method '.$method.' was not implemented in controller '.$controller.' of endpoint '.$endpoint.'.'
		);
	}

	public static function errorExecutingFunction(string $endpoint,string $controller,string $function,string $information):static{
		return new static(
			'errorExecutingMethod',
			'An error occurred while executing function '.$function.' of controller '.$controller.' in endpoint '.$endpoint.'.',
			$information
		);
	}

	public static function routeNotResolved(string $router,string $command,string $method):static{
		return new static(
			'routeNotResolved',
			'Route '.strtoupper($method).' '.$command.' was not resolved in '.$router.'.'
		);
	}

	public static function methodNotAllowed(string $router,string $command,string $method):static{
		return new static(
			'methodNotAllowed',
			'Method '.strtoupper($method).' was not allowed for '.$command.' in '.$router.'.'
		);
	}

}
