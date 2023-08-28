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

	public static function errorLoadingEndpoint():static{
		return new static(
			'errorLoadingEndpoint',
			'An error occurred while loading endpoint.'
		);
	}

	public static function errorLoadingRouter():static{
		return new static(
			'errorLoadingRouter',
			'An error occurred while loading endpoint router.'
		);
	}

	public static function errorLoadingCallback(string $method,string $command):static{
		return new static(
			'errorLoadingCallback',
			'An error occurred while loading callback '.strtoupper($method).':'.$command.'.'
		);
	}

	public static function errorLoadingController(string $controller):static{
		return new static(
			'errorLoadingController',
			'An error occurred while loading controller '.$controller.'.'
		);
	}

	public static function functionNotImplemented(string $controller,string $method):static{
		return new static(
			'functionNotImplemented',
			'Method '.$method.' was not implemented in controller '.$controller.'.'
		);
	}

	public static function errorExecutingFunction(string $controller,string $function,string $information):static{
		return new static(
			'errorExecutingMethod',
			'An error occurred while executing function '.$function.' of controller '.$controller.'.',
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
