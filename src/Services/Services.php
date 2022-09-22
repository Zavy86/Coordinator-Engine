<?php
/**
 * Services
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Services;

final class Services implements ServicesInterface{

	/** @var array Services with keys [uid]=Instance */
	protected static array $Services=[];

	private static function getService(string $uid):mixed{
		if(!array_key_exists($uid,static::$Services)){return false;}
		return static::$Services[$uid];
	}

	public static function exists(string $uid):bool{
		return array_key_exists(static::$Services);
	}

	public static function add(string $uid,mixed $Service):void{
		if(array_key_exists($uid,static::$Services)){
			throw ServicesException::uidDuplicated();
		}
		// @todo other checks?
		static::$Services[$uid]=$Service;
	}

	public static function getRequired(string $uid):mixed{
		$Service=static::getService($uid);
		if($Service===false){throw ServicesException::uidNotFound();}
		return $Service;
	}

	public static function getOptional(string $uid):mixed{
		$Service=static::getService($uid);
		if($Service===false){return null;}   // @todo log?
		return $Service;
	}

	public static function debug():array{
		$services=array();
		foreach(static::$Services as $uid=>$Service){
			$services[$uid]=$Service::class;
		}
		return $services;
	}

}
