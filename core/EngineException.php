<?php
/**
 * Engine Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine;

class EngineException extends \Exception{

	public const VERSION_FILE_NOT_FOUND=101;
	public static function versionFileNotFound():static{
		return new static("Version file not found.",static::VERSION_FILE_NOT_FOUND);
	}

	public const VERSION_SYNTAX_ERROR=201;
	public static function versionSyntaxError():static{
		return new static("Version file syntax error.",static::VERSION_SYNTAX_ERROR);
	}

}