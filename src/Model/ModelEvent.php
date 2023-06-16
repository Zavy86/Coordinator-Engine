<?php
/**
 * Abstract Model
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Model;

final class ModelEvent{

	public string $event;
	public int $timestamp;
	public string $account;
	public mixed $data;

	public function __construct(int $timestamp,string $account,string $event,mixed $data){
		$this->setTimestamp($timestamp);
		$this->setAccount($account);
		$this->setEvent($event);
		$this->setData($data);
	}

	public function getEvent():string{return $this->event;}
	public function getTimestamp():int{return $this->timestamp;}
	public function getAccount():string{return $this->account;}
	public function getData():mixed{return $this->data;}

	public function getProperties():array{
		$return=array();
		foreach(get_object_vars($this) as $property=>$value){
			$return[$property]=$this->$property;
		}
		return $return;
	}

	public function setEvent(string $value):void{
		if(!strlen($value)){throw ModelException::valueNotAcceptable(self::class,'event');}
		$this->event=$value;
	}

	public function setTimestamp(int $value):void{
		if($value<0){throw ModelException::valueNotAcceptable(self::class,'timestamp');}
		$this->timestamp=$value;
	}

	public function setAccount(string $value):void{
		if(!strlen($value)){throw ModelException::valueNotAcceptable(self::class,'account');}
		$this->account=$value;
	}

	public function setData(mixed $data):void{
		$this->data=$data;
	}

}
