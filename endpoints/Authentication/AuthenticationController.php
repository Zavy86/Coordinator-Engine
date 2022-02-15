<?php
/**
 * Authentication Controller
 *
 * @package Coordinator\Engine\Endpoints\Authentication
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoints\Authentication;

use Coordinator\Engine\Controller\AbstractController;

use Coordinator\Engine\Engine;
use Coordinator\Engine\Error\Error;
use Coordinator\Engine\Response\Response;

/**
 * Authentication Controller
 */
class AuthenticationController extends AbstractController{

	public function POST_authenticate(){
		/** @var LoginRequest $LoginRequestModel */
		$LoginRequestModel=$this->Request->getObject(LoginRequest::class);
		//var_dump($LoginRequestModel/*->debug()*/);
		$Session=Engine::getSession();
		// try to authenticate
		$authentication_success=$Session->authenticate(
			$LoginRequestModel->username,
			$LoginRequestModel->password,
			$LoginRequestModel->client,
			$LoginRequestModel->secret,
			$LoginRequestModel->duration
		);
		//var_dump($authentication_success);
		//var_dump($Session);
		if(!$authentication_success){
			$this->Response->setCode(Response::RC_401_UNAUTHORIZED);
			$this->Response->addError((new Error("authentication_failed",'Authentication failed using supplied parameters')));
		}else{
			$LoginResponseModel=new LoginResponse([
				"token"=>$Session->getToken(),
				"duration"=>$Session->getDuration(),
				"generation"=>$Session->getGeneration(),
				"expiration"=>$Session->getExpiration()
			]);
			//var_dump($LoginResponseModel);
			$this->Response->setObject($LoginResponseModel);
		}
	}

	public function GET_check(){
		$CheckResponseModel=new CheckResponse();
		$Session=Engine::getSession();
		//var_dump($Session);
		if($Session->isValid()){
			$CheckResponseModel->setProperties([
				"valid"=>true,
				"address"=>$Session->getAddress(),
				"username"=>$Session->getUsername(),
				"client"=>$Session->getClient(),
				"duration"=>$Session->getDuration(),
				"remaining"=>$Session->getRemaining(),
				"generation"=>$Session->getGeneration(),
				"expiration"=>$Session->getExpiration()
			]);
		}else{
			$this->Response->setCode(Response::RC_401_UNAUTHORIZED);
			$CheckResponseModel->setProperties([
				"valid"=>false
			]);
		}
		$this->Response->setObject($CheckResponseModel);
	}

}