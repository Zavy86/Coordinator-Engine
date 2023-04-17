<?php
/**
 * Response
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Response;

use Coordinator\Engine\Error\ErrorInterface;
use Coordinator\Engine\Model\ModelInterface;
use Coordinator\Engine\Object\ObjectInterface;

final class Response implements ResponseInterface{

	protected ResponseCode $Code;
	protected ObjectInterface $Object;
	protected array $Errors=[];

	public function __construct(){
		$this->setHeaders();
		$this->setCode(ResponseCode::INTERNAL_SERVER_ERROR_500);
	}

	private function setHeaders():void{
		header('Content-Type:application/json;charset=UTF-8');
		header('Access-Control-Max-Age:3600');
		header('Access-Control-Allow-Origin:'.($_SERVER['HTTP_ORIGIN']??'*'));
		header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS');
		header('Access-Control-Allow-Headers:Origin,Accept,Authorization,Token,Content-Type,Access-Control-Allow-Origin,Access-Control-Allow-Headers,X-Requested-With');
		if($_SERVER['REQUEST_METHOD']=='OPTIONS'){$this->setCode(ResponseCode::OK_200);exit(0);}
	}

	public function getCode():ResponseCode{
		return $this->Code;
	}

	public function setCode(ResponseCode $ResponseCode):void{
		$this->Code=$ResponseCode;
		http_response_code($ResponseCode->value);
	}

	public function getErrors():array{
		return $this->Errors;
	}

	public function addError(ErrorInterface $Error):void{
		$this->Errors[]=$Error;
	}

	public function getObject():?ObjectInterface{
		return $this->Object??null;
	}

	public function setObject(ObjectInterface $Object):void{
		$this->Object=$Object;
	}

	public function render():string{
		$response=array(
			"error"=>(bool)count($this->getErrors()),
			"errors"=>array(),
			"object"=>'',
			"data"=>new \stdClass()
		);
		if(isset($this->Object)){
			$response['object']=$this->Object::class;
			$response['data']=$this->Object->getProperties();
		}
		/** @var ErrorInterface $Error */
		foreach($this->getErrors() as $Error){
			$response['errors'][]=$Error->output();
		}

		//sleep(3);

		if(!count($this->getErrors())){$this->setCode(ResponseCode::OK_200);}
		return json_encode($response,JSON_PRETTY_PRINT);
	}

	public function debug():array{
		return array(
			'class'=>$this::class,
			'code'=>$this->getCode(),
			'Errors'=>$this->getErrors(),
			'Object'=>(isset($this->Object)?$this->Object->debug():null)
		);
	}

}
