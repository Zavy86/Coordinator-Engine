<?php
/**
 * Response
 *
 * @package Coordinator\Response
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Response;

use Coordinator\Engine\Error\ErrorInterface;
use Coordinator\Engine\Object\ObjectInterface;

final class Response implements ResponseInterface{

	/**
	 * Constants     /@todo spostare fuori da response in un ENUM ?
	 */
	const RC_200_OK=200;
	const RC_201_CREATED=201;
	const RC_301_MOVED_PERMANENTLY=301;
	const RC_400_BAD_REQUEST=400;
	const RC_401_UNAUTHORIZED=401;
	const RC_403_FORBIDDEN=403;
	const RC_404_NOT_FOUND=404;
	const RC_405_METHOD_NOT_ALLOWED=405;
	const RC_429_TOO_MANY_REQUESTS=429;
	const RC_500_INTERNAL_SERVER_ERROR=500;
	const RC_501_NOT_IMPLEMENTED=501;
	const RC_503_SERVICE_UNAVAILABLE=503;

	protected int $code=Response::RC_500_INTERNAL_SERVER_ERROR;
	protected array $Errors=[];
	protected ObjectInterface $Object;

	public function __construct(){
/*  @todo verificare per CORS
		// Allow from any origin
		if (isset($_SERVER['HTTP_ORIGIN'])) {
			// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
			// you want to allow, and if so:
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');    // cache for 1 day
		}
		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
				// may also be using PUT, PATCH, HEAD etc
				header("Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS");
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
			exit(0);
		}
*/
		header("Content-Type:application/json;charset=UTF-8");
		header("Access-Control-Max-Age:3600");
		header("Access-Control-Allow-Origin:".($_SERVER['HTTP_ORIGIN']??'*'));
		header("Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS");
		header("Access-Control-Allow-Headers:Origin,Accept,Authorization,Token,Content-Type,Access-Control-Allow-Origin,Access-Control-Allow-Headers,X-Requested-With");
		http_response_code($this->getCode());
	}

	final public function getCode():int{return $this->code;}
	final public function getErrors():array{return $this->Errors;}
	final public function getObject():?ObjectInterface{return $this->Object??null;}

	final public function setCode(int $code){
		if(0){throw ResponseException::genericError();}   /* @todo throw */
		$this->code=$code;
		http_response_code($code);
	}

	public function addError(ErrorInterface $Error){
		if(0){throw ResponseException::genericError();}   /* @todo throw */
		$this->Errors[]=$Error;
	}

	final public function setObject(ObjectInterface $Object){
		if(0){throw ResponseException::genericError();}
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