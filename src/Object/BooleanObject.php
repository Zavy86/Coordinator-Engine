<?php
/**
 * Boolean Object
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Object;

class BooleanObject extends AbstractObject{

	public bool $response;

	private static function build(bool $response):static{
		return new static(['response'=>$response]);
	}

	public static function true():static{
		return static::build(true);
	}

	public static function false():static{
		return static::build(false);
	}

	public function setResponse(bool $response):void{
		$this->response=$response;
	}

	public function setTrue():void{
		$this->setResponse(true);
	}

	public function setFalse():void{
		$this->setResponse(false);
	}

}
