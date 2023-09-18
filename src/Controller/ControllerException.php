<?php
/**
 * Controller Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Controller;

final class ControllerException extends \Exception{

	final public static function genericException(string $exception):static{
		return new static($exception);
	}

}
