<?php
/**
 * Handler
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Handler;

use Coordinator\Engine\Engine;

use Coordinator\Engine\Request\RequestInterface;
use Coordinator\Engine\Response\ResponseInterface;
use Coordinator\Engine\Response\ResponseCode;

use Coordinator\Engine\Endpoint\EndpointInterface;
use Coordinator\Engine\Router\RouterException;
use Coordinator\Engine\Router\RouterInterface;
use Coordinator\Engine\Callback\CallbackInterface;
use Coordinator\Engine\Controller\ControllerInterface;

final class Handler implements HandlerInterface{

	/**
	 * Interfaces
	 */
	private EndpointInterface $Endpoint;
	private RouterInterface $Router;
	private CallbackInterface $Callback;
	private ControllerInterface $Controller;

	/**
	 * Constructor
	 *
	 * @param RequestInterface $Request
	 * @param ResponseInterface $Response
	 *
	 * @throws HandlerException
	 */
	final public function __construct(
		private RequestInterface $Request,
		private ResponseInterface $Response
	){
		$this->initialize();
	}

	private function initialize():void{
		try{
			$this->loadEndpoint();
			$this->loadRouter();
			$this->loadCallback();
			$this->loadController();
			$this->checkFunction();
		}catch(HandlerException $Exception){
			switch($Exception->getCode()){
				case HandlerException::ERROR_LOADING_ENDPOINT:
					$this->Response->addError(HandlerError::errorLoadingEndpoint($this->getEndpointName()));
					$this->Response->setCode(ResponseCode::INTERNAL_SERVER_ERROR_500);
					break;
				case HandlerException::ERROR_LOADING_ROUTER:
					$this->Response->addError(HandlerError::errorLoadingRouter($this->getEndpointName()));
					$this->Response->setCode(ResponseCode::INTERNAL_SERVER_ERROR_500);
					break;
				case HandlerException::ERROR_LOADING_CALLBACK:
					$this->Response->addError(HandlerError::errorLoadingCallback($this->Request->getMethod(),$this->Request->getUri()));
					$this->Response->setCode(ResponseCode::INTERNAL_SERVER_ERROR_500);
					break;
				case HandlerException::ERROR_LOADING_CONTROLLER:
					$this->Response->addError(HandlerError::errorLoadingController($this->getEndpointName(),$this->Callback->getController()));
					$this->Response->setCode(ResponseCode::INTERNAL_SERVER_ERROR_500);
					break;
				case HandlerException::FUNCTION_NOT_IMPLEMENTED:
					$this->Response->addError(HandlerError::functionNotImplemented($this->getEndpointName(),$this->Callback->getController(),$this->Callback->getFunction()));
					$this->Response->setCode(ResponseCode::NOT_IMPLEMENTED_501);
					break;
				case HandlerException::METHOD_NOT_ALLOWED:
					$this->Response->addError(HandlerError::methodNotAllowed($this->getEndpointName(),$this->Request->getUri(),$this->Request->getMethod()));
					$this->Response->setCode(ResponseCode::METHOD_NOT_ALLOWED_405);
					break;
				default:
					$this->Response->addError(HandlerError::uncatchedError($Exception->getMessage()));
					$this->Response->setCode(ResponseCode::INTERNAL_SERVER_ERROR_500);
			}
		}
	}

	private function getEndpointName():string{
		return explode("/",$this->Request->getUri())[1];
	}

	private function loadEndpoint():void{
		//$endpoint=$this->getEndpointName();
		//$endpointClass='\Coordinator\Engine\Endpoints\\'.$endpoint.'\\'.'Endpoint';
		//$endpointClass=Engine::$NAMESPACE.'\\'.$endpoint.'\\'.'Endpoint';
		$endpointClass=Engine::$NAMESPACE.'\\'.'Endpoint';
		//var_dump($endpointClass);
		try{
			$this->Endpoint=new $endpointClass;
			//var_dump($this->Endpoint);
		}catch(\Exception|\TypeError $Exception){
			//var_dump($Exception->getMessage());
			throw HandlerException::errorLoadingEndpoint($endpoint);
		}
	}

	private function loadRouter():void{
		//$endpoint=$this->getEndpointName();
		//$routerClass='\Coordinator\Engine\Endpoints\\'.$endpoint.'\\'.'Router';
		//$routerClass=Engine::$NAMESPACE.'\\'.$endpoint.'\\'.'Router';
		$routerClass=Engine::$NAMESPACE.'\\'.'Router';
		//var_dump($routerClass);
		try{
			$this->Router=new $routerClass;
			//var_dump($this->Router);
		}catch(\Exception|\TypeError $Exception){
			//var_dump($Exception->getMessage());
			throw HandlerException::errorLoadingRouter($endpoint);
		}
	}

	private function loadCallback():void{
		try{
			$this->Callback=$this->Router->resolveRoute($this->Request->getMethod(),$this->Request->getUri());
			//var_dump($this->Callback);
		}catch(\Exception|\TypeError $Exception){
			// @todo qui scattano le eccezioni da gestire in maniera diversa ROUTE_NOT_RESOLVED e METHOD_NOT_ALLOWED
			//var_dump($Exception);
			//var_dump($Exception->getMessage());
			if($Exception->getCode()==RouterException::METHOD_NOT_ALLOWED){
				throw HandlerException::methodNotAllowed($this->Request->getMethod(),$this->Request->getUri());
			}else{
				throw HandlerException::errorLoadingCallback($this->Request->getMethod(),$this->Request->getUri());
			}
		}
	}

	private function loadController():void{
		$controllerClass=$this->Callback->getController();
		//var_dump($controllerClass);
		try{
			$this->Controller=new $controllerClass($this->Request,$this->Response);
		}catch(\Exception|\TypeError $Exception){
			//var_dump($Exception->getMessage());
			throw HandlerException::errorLoadingController($this->getEndpointName(),$this->Callback->getController());
		}
	}

	private function checkFunction():void{
		if(!method_exists($this->Callback->getController(),$this->Callback->getFunction())){
			throw HandlerException::functionNotImplemented($this->getEndpointName(),$this->Callback->getController(),$this->Callback->getFunction());
		}
	}

	public function handle():void{
		try{
			//if(isset($this->Callback)){
			$function=$this->Callback->getFunction();
			//}
			//if(isset($this->Controller)){
			$this->Controller->$function();
			//}
		}catch(\Exception $Exception){
			// if in debug mode show real error
			if(Engine::$DEBUG){throw $Exception;}
			// show generic error for client
			$this->Response->addError(HandlerError::errorExecutingFunction($this->getEndpointName(),$this->Callback->getController(),$this->Callback->getFunction()));
			$this->Response->setCode(ResponseCode::INTERNAL_SERVER_ERROR_500);
		}finally{
			$this->log();  // valutare se mettere qui o dentro un try
			$this->render(); // dovrebbe stare qui perche dovrò sempre cercare di effetuare un rendering
		}
	}

	/**
	 * Log
	 */
	public function log():void{
		/* @todo riabilitare quando riattivo il service

		/** @var TokenSession $TokenSession *
		$TokenSession=Services::getService("session");
		//is_a($TokenSession,TokenSession::class) &&
		if($TokenSession->getToken()){   // @todo valutare come fare in modo che si verifichi la sessione prima di fare il log in modo da loggare anche richieste non sessionate
		$Log=new SystemLogModel();
		$Log->setProperty('timestamp',time());
		$Log->setProperty('token',$TokenSession->getToken());
		$Log->setProperty('module',$this->getModule());
		$Log->setProperty('command',$this->command_full); //getCommand());  @todo correggere ovunque
		$Log->setProperty('remoteAccount',$TokenSession->getRemoteAccount());
		$Log->setProperty('remoteAddress',$TokenSession->getRemoteAddress());
		$Log->setProperty('response',$this->Response->getCode());
		$Log->setErrors($this->Response->getErrors());
		//var_dump($Log);
		$Log->save();
		}
		 */
	}

	/**
	 * Render response
	 */
	private function render():void{
		echo $this->Response->render();
	}

	public function debug():array{
		return array(
			'Request'=>(isset($this->Request)?$this->Request->debug():null),
			'Endpoint'=>(isset($this->Endpoint)?$this->Endpoint->debug():null),
			'Router'=>(isset($this->Router)?$this->Router->debug():null),
			'Callback'=>(isset($this->Callback)?$this->Callback->debug():null),
			'Controller'=>(isset($this->Controller)?$this->Controller->debug():null),
			'Response'=>(isset($this->Response)?$this->Response->debug():null)
		);
	}

	public function getRequest():RequestInterface{
		return $this->Request;
	}

	public function getResponse():ResponseInterface{
		return $this->Response;
	}

}
