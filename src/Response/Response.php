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
	protected ObjectInterface|ModelInterface $Object;
	protected array $Errors=[];

	public function __construct(){
		$this->setHeaders();
		$this->setCode(ResponseCode::INTERNAL_SERVER_ERROR_500);
	}

	private function setHeaders():void{
		/*
		// Allow from any origin
		if (isset($_SERVER['HTTP_ORIGIN'])) {
		// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
		// you want to allow, and if so:
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Max-Age: 86400');    // cache for 1 day
		}
		// Access-Control headers are received during OPTIONS requests
		if($_SERVER['REQUEST_METHOD']=='OPTIONS'){
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		// may also be using PUT, PATCH, HEAD etc
		header("Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS");
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
		exit(0);
		}*/
		header("Content-Type:application/json;charset=UTF-8");
		header("Access-Control-Max-Age:3600");
		header("Access-Control-Allow-Origin:".($_SERVER['HTTP_ORIGIN']??'*'));
		header("Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS");
		header("Access-Control-Allow-Headers:Origin,Accept,Authorization,Token,Content-Type,Access-Control-Allow-Origin,Access-Control-Allow-Headers,X-Requested-With");
		//  @todo verificare per CORS
		if($_SERVER['REQUEST_METHOD']=='OPTIONS'){$this->setCode(ResponseCode::OK_200);die();}
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

	public function getObject():ObjectInterface|ModelInterface|null{
		return $this->Object??null;
	}

	public function setObject(ObjectInterface|ModelInterface $Object):void{
		$this->Object=$Object;
	}

	public function render():string{
		$response=array(
		 "error"=>(bool)count($this->getErrors()),
		 "errors"=>array(),
		 "object"=>'',
		 "data"=>array()
		);
		if(isset($this->Object)){
			$response['object']=$this->Object::class;
			$response['data']=$this->Object->getProperties();
		}
		/** @var ErrorInterface $Error */
		foreach($this->getErrors() as $Error){
			$response['errors'][]=$Error->output();
		}
		
		sleep(3);
		
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
