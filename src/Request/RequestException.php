<?php
/**
 * Request Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Request;

final class RequestException extends \Exception{

	public const METHOD_NOT_ALLOWED=101;
	public static function methodNotAllowed(string $method):static{
		return new static("Method ".$method." was not allowed.",static::METHOD_NOT_ALLOWED);
	}

	public const OBJECT_TYPE_MISMATCH=201;
	public static function objectTypeMismatch(string $object):static{
		return new static("Object ".$object." must implement ObjectInterface.",static::OBJECT_TYPE_MISMATCH);
	}

}
