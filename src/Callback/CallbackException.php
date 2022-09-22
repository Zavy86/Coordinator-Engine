<?php
/**
 * Callback Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Callback;

final class CallbackException extends \Exception{

	public const CONTROLLER_NOT_FOUND=101;
	public static function controllerNotFound(string $controller):static{
		return new static('Callback controller '.$controller.' was not found.',static::CONTROLLER_NOT_FOUND);
	}

	public const CONTROLLER_TYPE_MISMATCH=102;
	public static function controllerTypeMismatch(string $controller):static{
		return new static('Callback controller '.$controller.' must implement ControllerInterface.',static::CONTROLLER_TYPE_MISMATCH);
	}

}
