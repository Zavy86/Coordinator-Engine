<?php
/**
 * Client Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Client;

final class ClientException extends \Exception{

	public const SESSION_EXPIRED=101;
	public static function sessionExpired():static{
		return new static('Current session token is expired',static::SESSION_EXPIRED);
	}

	public const RESULT_ERRORS=102;
	public static function resultErrors(array $errors):static{
		return new static('Errors in results: '.json_encode($errors),static::RESULT_ERRORS);
	}

}
