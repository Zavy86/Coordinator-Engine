<?php
/**
 * Configuration Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Configuration;

final class ConfigurationException extends \Exception{

	public const CONFIGURATION_FILE_NOT_FOUND=101;
	public static function configurationFileNotFound(string $configurationFilePath):static{
		return new static('Configuration file '.$configurationFilePath.' not exists.',static::CONFIGURATION_FILE_NOT_FOUND);
	}

	public const CONFIGURATION_SYNTAX_ERROR=102;
	public static function configurationSyntaxError():static{
		return new static('Configuration syntax error.',static::CONFIGURATION_SYNTAX_ERROR);
	}
	public const CONFIGURATION_PARAMETER_NOT_FOUND=103;
	public static function configurationParameterNotFound(string $parameter):static{
		return new static('Expected parameter '.$parameter.' in configuration.',static::CONFIGURATION_PARAMETER_NOT_FOUND);
	}

	public const PROPERTY_NOT_FOUND=201;
	public static function propertyNotFound(string $class,string $property):static{
		return new static('Property '.$property.' is not defined in '.$class.'.',static::PROPERTY_NOT_FOUND);
	}

}