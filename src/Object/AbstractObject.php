<?php
/**
 * Abstract Object
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Object;

abstract class AbstractObject implements ObjectInterface{

	public array $relates=[];

	public function __construct(array $properties=array()){
		$this->setProperties($properties);
		if(method_exists($this,"initialization")){
			$this->initialization();
		}
	}

	final public function isSet($property):bool{
		$rp=new \ReflectionProperty(static::class,$property);
		//var_dump($rp->isInitialized($this));
		//if($rp->isInitialized($this)){var_dump($this->{$property});}
		return $rp->isInitialized($this);
	}

	final public function setProperties(array $properties=array()):void{
		foreach($properties as $property=>$value){
			if(!in_array($property,array_keys(get_class_vars($this::class)))){
				throw ObjectException::propertyNotExists($this::class,$property);
			}
			$this->$property=$value;
		}
	}

	final public function getProperties():array{
		$response=get_class_vars($this::class);
		foreach(get_object_vars($this) as $property=>$value){
			if($value instanceof ObjectInterface){$response[$property]=$value->getProperties();}
			else{$response[$property]=$value;}
		}
		unset($response['relates']);
		return $response;
	}

	final public function get(string $property):mixed{
		if(!in_array($property,array_keys(get_class_vars($this::class)))){
			throw ObjectException::propertyNotExists($this::class,$property);
		}
		return $this->$property;
	}
	/*
		final public function addRelated(string $name,mixed $value,bool $overwrite=true):bool{
			if(!$overwrite){if(array_key_exists($name,$this->relates)){return false;}}
			$this->relates[$name]=$value;
			return true;
		}

		final public function getRelated(string $name):mixed{
			if(!array_key_exists($name,$this->relates)){
				//throw ObjectException::propertyNotExists($this::class,$property);
				return null;
			}
			return $this->relates[$name];
		}
	*/
	final public function debug():array{
		$properties=$this->getProperties();
		foreach($properties as $property=>$value){
			if(str_contains($property,"password")||str_contains($property,"secret")){
				$properties[$property]=$this::maskProperty($value);
			}
		}
		return $properties;
	}

	private static function maskProperty(?string $value=null):?string{
		if(is_null($value)){return null;}
		if(strlen($value)<=6){return "********";}
		return substr($value,0,2).str_repeat("*",(strlen($value)-4)).substr($value,-2);
	}

}
