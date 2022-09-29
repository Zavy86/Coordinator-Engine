<?php
/**
 * Storage Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Storage;

final class StorageException extends \Exception{

	public const GENERIC_EXCEPTION=101;
	final public static function genericException(string $exception):static{
		return new static($exception,self::GENERIC_EXCEPTION);
	}

	public static function pathUnavailable($path):static{
		return new static("Path ".$path." was not available.");
	}

	public static function pathNotWritable($path):static{
		return new static("Path ".$path." was not writable.");
	}

	public static function invalidFormat():static{
		return new static("File format was not valid.");
	}

	public static function uidNotAvailable(mixed $uid):static{
		return new static("UID ".$uid." was not valid.");
	}

	public static function notAvailable(string $key,mixed $value):static{
		return new static("Model with key ".$key."=".$value." was not available.");
	}

	public static function cacheError():static{
		return new static("Unable to load locate cache.");
	}

	public static function savingError(?string $error=null):static{
		return new static("Error saving. ".$error);
	}

	public static function datasetNotDefined():static{
		return new static("Model dataset was not defined");
	}

	public static function countError():static{
		return new static("Error executing count");
	}

}
