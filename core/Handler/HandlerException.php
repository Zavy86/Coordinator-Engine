<?php
/**
 * Handler Exception
 *
 * @package Coordinator\Engine\Handler
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Handler;

class HandlerException extends \Exception{

	public const MODULE_NOT_FOUND=101;
	public static function moduleNotFound(string $module):static{
		return new static('Module '.$module.' was not found.',static::MODULE_NOT_FOUND);
	}

	public const ROUTER_NOT_FOUND=201;
	public static function routerNotFound(string $module):static{
		return new static('There is no router in module '.$module.'.',static::MODULE_NOT_FOUND);
	}

	public const ERROR_LOADING_ROUTER=202;
	public static function errorLoadingRouter(string $module):static{
		return new static('An error occurred while loading module '.$module.' router.',static::ERROR_LOADING_ROUTER);
	}

	public const CONTROLLER_NOT_FOUND=301;
	public static function controllerNotFound(string $module,string $controller):static{
		return new static('Controller '.$controller.' was not found in module '.$module.'.',static::CONTROLLER_NOT_FOUND);
	}

	public const ERROR_LOADING_CONTROLLER=302;
	public static function errorLoadingController(string $module,string $controller):static{
		return new static('An error occurred while loading controller '.$controller.' of module '.$module.'.',static::ERROR_LOADING_CONTROLLER);
	}

	public const METHOD_NOT_FOUND=401;
	public static function methodNotFound(string $module,string $controller,string $method):static{
		return new static('Method '.$method.' was not found in controller '.$controller.' of module '.$module.'.',static::METHOD_NOT_FOUND);
	}

	public const ERROR_EXECUTING_METHOD=402;
	public static function errorExecutingMethod(string $module,string $controller,string $method):static{
		return new static('An error occurred while executing method '.$method.' of controller '.$controller.' in module '.$module.'.',static::ERROR_EXECUTING_METHOD);
	}

}