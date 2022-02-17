<?php
/**
 * Abstract Controller
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Controller;

use Coordinator\Engine\Engine;
use Coordinator\Engine\Error\Error;
use Coordinator\Engine\Request\RequestInterface;
use Coordinator\Engine\Response\ResponseInterface;
use Coordinator\Engine\Response\ResponseCode;

abstract class AbstractController implements ControllerInterface{

	public function __construct(
	 protected RequestInterface $Request,
	 protected ResponseInterface $Response
	){}

	// @todo implementare
	protected function check(string $authorization):bool{
		$authorized=true;
		if(!$this->checkSessionValidity()){
			$authorized=false;
			$this->Response->addError((new Error("authenticationInvalid",'Authentication token provided is not valid')));
		}
		if(strlen($authorization) && !$this->checkAuthorization($authorization)){
			$authorized=false;
			$this->Response->addError((new Error("authorizationDenied",'You have not the authorization to perform this operation')));
		}
		if($authorized==false){$this->Response->setCode(ResponseCode::UNAUTHORIZED_401);}
		return $authorized;
	}

	private function checkSessionValidity():bool{
		$Session=Engine::getSession();
		//var_dump($Session);
		return $Session->isValid();
	}

	private function checkAuthorization(string $authorization):bool{  // @todo fare classe specifica
		// @todo implementare
		return true;
	}

	public function debug():array{
		return array(
			'class'=>$this::class
			//'Request'=>$this->Request->debug(),
			//'Response'=>$this->Request->debug()
		);
	}

}