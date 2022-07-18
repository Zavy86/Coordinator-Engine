<?php
/**
 * Error
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Error;

/**
 * Error class
 */
final class Error extends AbstractError{

	final public static function genericError(string $description,?string $information=null):static{
		return new static(
			'genericError',
			$description,
			$information
		);
	}
}