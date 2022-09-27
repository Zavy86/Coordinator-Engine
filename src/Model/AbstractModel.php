<?php
/**
 * Abstract Model
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Model;

use Coordinator\Engine\Services\Services;
use Coordinator\Engine\Storage\StorageInterface;
use Coordinator\Engine\Filter\FilterInterface;
use Coordinator\Engine\Sorting\SortingInterface;
use Coordinator\Engine\Pagination\PaginationInterface;

abstract class AbstractModel implements ModelInterface{

	/**
	 * Static Properties
	 */

	/** @var int $_uid_length UID character length (8 to 32) */
	static protected int $_uid_length=8;
	/** @var string $_service Storage Service */
	static public string $_service;

	//static private array $properties=[];

	/**
	 * Model Properties
	 */

	protected mixed $uid=null;



	/*static protected function setProperties(string ...$properties){
		foreach(array_filter($properties) as $property){
			static::$properties[$property]=null;
		}
	}*/

	final public function getUid():?string{return $this->uid;}

	final protected function setUid(mixed $uid):?bool{
		if(!is_null($this->getUid())){return false;}  // @todo throw instead of boolean?
		if(!strlen($uid)){throw ModelException::uidNull();}
		$this->uid=$uid;
		return true;
	}

	protected function generateUid():?bool{
		if(!is_null($this->getUid())){return false;}
		$length=static::$_uid_length;
		if($length<8){$length=8;}
		if($length>32){$length=32;}
		do{
			$uid=substr(md5(time().rand(1000,9999)),0,$length);
		}while(static::exists($uid));
		return $this->setUid($uid);      // decidere se sopra tengo bool o throw e in caso fare un try (magari dentro il loop gestendo un max per evitare l'INFL)
	}

	public function getProperty($property):mixed{
		if(!in_array($property,array_keys(get_object_vars($this)))){return false;}  // @todo throw instead of boolean?
		return $this->$property;;
	}

	public function __get($property):mixed{             /** @todo valutare se tenere o meno */
		if(!in_array($property,array_keys(get_object_vars($this)))){return false;}  // @todo throw instead of boolean?
		return $this->$property;;
	}

	public function getProperties():array{
		$return=array();
		foreach(get_object_vars($this) as $property=>$value){
			//if(in_array($property,array("uid"))){continue;}
			$return[$property]=$this->$property;
		}
		return $return;
	}

	/** @check */
	/*public function setProperties(array $properties):int{
		$set_property_counter=0;
		foreach($properties as $property=>$value){
			if($this->setProperty($property,$value)){$set_property_counter++;}
		}
		return $set_property_counter;
	}*/
	// @todo verificare che siano stati compilati tutti i campi obbligatori
	final public function setProperties(array $properties=array()):void{
		foreach($properties as $property=>$value){
			if(!in_array($property,array_keys(get_class_vars($this::class)))){
				throw ModelException::propertyNotExists($this::class,$property);
			}
			$this->$property=$value;
		}
	}

	public function setProperty(string $property,mixed $value):bool{
		if(0){return false;}                                                /** @todo check */
		if($property=="uid"){return false;}          /** @todo logger */
		if(!in_array($property,array_keys(get_class_vars($this::class)))){throw ModelException::propertyNotExists(static::class,$property);}          /** @todo logger */
		$this->$property=$value;
		return true;
	}



	private static function getStorageService():StorageInterface{
		//echo "<br>service ".static::$service;
		//echo "<br>class ".static::class;
		// @todo try and checks
		return Services::getRequired(static::$_service);
	}

	protected static function loadFrom(string $key,mixed $value):ModelInterface{   // @todo come gia scritto nell'interfaccia valutare sostituzione con browse filters
		$Model=static::getStorageService()->loadFromKey(new static,$key,$value,$uid);
		$Model->setUid($uid);
		return $Model;
	}

	protected static function loadFromKeys(array $keys):ModelInterface{   // @todo come sopra
		$Model=static::getStorageService()->loadFromKeys(new static,$keys,$uid);
		$Model->setUid($uid);
		return $Model;
	}

	public static function count(?FilterInterface $Filters=null):int{
		return static::getStorageService()->count(new static,$Filters);
	}

	public static function browse(?FilterInterface $Filters=null,?SortingInterface $Sorting=null,?PaginationInterface $Pagination=null):array{
		return static::getStorageService()->browse(new static,$Filters,$Sorting,$Pagination);
	}

	public static function exists(mixed $uid):bool{
		return static::getStorageService()->exists(new static,$uid);
	}

	public static function load(mixed $uid):static{
		$Model=static::getStorageService()->load(new static,$uid);
		$Model->setUid($uid);
		return $Model;
	}

	public function save():bool{
		if(!$this->getUid()){$this->generateUid();}
		return static::getStorageService()->save($this);
	}

	public function remove():bool{
		if(!$this->getUid()){throw ModelException::cannotDeleteWithoutUID();}
		return static::getStorageService()->remove($this);
	}

	public function debug():array{   // @todo parametro masked property ? per password o altri dati sensibili
		$debug=array('uid'=>$this->getUid());
		foreach($this->getProperties() as $property=>$value){
			if(str_contains($property,"password")||str_contains($property,"secret")){
				$debug[$property]=$this::maskProperty($value);
			}else{
				$debug[$property]=$value;
			}
		}
		return $debug;
	}

	private static function maskProperty(?string $value=null):?string{
		if(is_null($value)){return null;}
		if(strlen($value)<=6){return "********";}
		return substr($value,0,2).str_repeat("*",(strlen($value)-4)).substr($value,-2);
	}

}
