<?php
/**
 * Abstract Configuration
 *
 * @package Coordinator\Engine\Configuration
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Configuration;

abstract class AbstractConfiguration implements ConfigurationInterface{

	public function __construct(string $configurationFilePath){
		if(!file_exists($configurationFilePath)){throw ConfigurationException::configurationFileNotFound($configurationFilePath);}
		$bytes=file_get_contents($configurationFilePath);
		$parameters=json_decode($bytes,true);
		if(!is_array($parameters)){throw ConfigurationException::configurationFileSyntaxError($configurationFilePath);}
		foreach(array_keys(get_class_vars($this::class)) as $property){
			if(!isset($parameters[$property])){throw ConfigurationException::configurationFileParameterNotFound($configurationFilePath,$property);}
			$this->$property=$parameters[$property];
		}
	}

	public function get(string $property):mixed{
		if(!key_exists($property,get_class_vars($this::class))){throw ConfigurationException::propertyNotFound(static::class,$property);}
		return $this->$property;
	}

	public function __debugInfo():?array{
		$properties=get_object_vars($this);   // @todo object or get_class_vars ?
		foreach($properties as $property=>$value){
			if(str_contains($property,"password")){
				$properties[$property]=$this::maskProperty($value);
			}
		}
		return $properties;
	}

	private static function maskProperty(?string $value):?string{
		if(is_null($value)){return null;}
		if(strlen($value)<=6){return "********";}
		return substr($value,0,2).str_repeat("*",(strlen($value)-4)).substr($value,-2);
	}

}