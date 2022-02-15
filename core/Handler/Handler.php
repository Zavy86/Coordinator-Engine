<?php
/**
 * Handler
 *
 * @package Coordinator\Engine\Handler
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Handler;

use Coordinator\Engine\Engine;

use Coordinator\Engine\Error\HandlerError;

use Coordinator\Engine\Request\RequestInterface;
use Coordinator\Engine\Response\ResponseInterface;
use Coordinator\Engine\Response\Response;

use Coordinator\Engine\Router\RouterException;

use Coordinator\Engine\Router\RouterInterface;
use Coordinator\Engine\Router\CallbackInterface;
use Coordinator\Engine\Controller\ControllerInterface;

class Handler implements HandlerInterface{

	private string $module;
	private string $command;

	/**
	 * Interfaces
	 */
	private RouterInterface $Router;
	private ControllerInterface $Controller;
	private CallbackInterface $Callback;

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

	public function getModule():string{return $this->module;}
	public function getCommand():string{return $this->command;}

	private function initialize():void{
		try{
			$this->parseUri();
			$this->checkModule();
			$this->checkRouter();
			$this->loadRouter();

			$this->Callback=$this->Router->resolveRoute($this->Request->getMethod(),$this->getCommand());

			$this->checkCallback();
			$this->loadController();
		}catch(HandlerException $Exception){
			switch($Exception->getCode()){
				case HandlerException::MODULE_NOT_FOUND:
					$this->Response->addError(HandlerError::moduleNotFound($this->getModule()));
					$this->Response->setCode(Response::RC_404_NOT_FOUND);
					break;
				case HandlerException::ROUTER_NOT_FOUND:
					$this->Response->addError(HandlerError::routerNotFound($this->getModule()));
					$this->Response->setCode(Response::RC_500_INTERNAL_SERVER_ERROR);
					break;
				case HandlerException::ERROR_LOADING_ROUTER:
					$this->Response->addError(HandlerError::errorLoadingRouter($this->getModule()));
					$this->Response->setCode(Response::RC_500_INTERNAL_SERVER_ERROR);
					break;
				case HandlerException::CONTROLLER_NOT_FOUND:
					$this->Response->addError(HandlerError::controllerNotFound($this->getModule(),$this->Callback->getController()));
					$this->Response->setCode(Response::RC_501_NOT_IMPLEMENTED);
					break;
				case HandlerException::ERROR_LOADING_CONTROLLER:
					$this->Response->addError(HandlerError::errorLoadingController($this->getModule(),$this->Callback->getController()));
					$this->Response->setCode(Response::RC_500_INTERNAL_SERVER_ERROR);
					break;
				case HandlerException::METHOD_NOT_FOUND:
					$this->Response->addError(HandlerError::methodNotFound($this->getModule(),$this->Callback->getController(),$this->Callback->getMethod()));
					$this->Response->setCode(Response::RC_501_NOT_IMPLEMENTED);
					break;
				case HandlerException::ERROR_EXECUTING_METHOD:
					$this->Response->addError(HandlerError::errorExecutingMethod($this->getModule(),$this->Callback->getController(),$this->Callback->getMethod()));
					$this->Response->setCode(Response::RC_500_INTERNAL_SERVER_ERROR);
					break;
				default:
					$this->Response->addError(HandlerError::uncatchedError($Exception->getMessage()));
					$this->Response->setCode(Response::RC_500_INTERNAL_SERVER_ERROR);
			}
		}catch(RouterException $Exception){
			switch($Exception->getCode()){
				case RouterException::ROUTE_NOT_RESOLVED:
					$this->Response->addError(HandlerError::routeNotResolved($this->Router::class,$this->getCommand(),$this->Request->getMethod()));
					$this->Response->setCode(Response::RC_404_NOT_FOUND);
					break;
				case RouterException::METHOD_NOT_ALLOWED:
					$this->Response->addError(HandlerError::methodNotAllowed($this->Router::class,$this->getCommand(),$this->Request->getMethod()));
					$this->Response->setCode(Response::RC_405_METHOD_NOT_ALLOWED);
					break;
				default:
					$this->Response->addError(HandlerError::uncatchedError($Exception->getMessage()));
					$this->Response->setCode(Response::RC_500_INTERNAL_SERVER_ERROR);
			}
		}
	}

	protected function parseUri():void{
		$uri=$this->Request->getUri();
		if($uri)
		$this->command=$uri;
		$this->module=explode("/",$this->command)[1];
	}

	private function checkModule():void{
		$module=$this->getModule();
		if(!is_dir(Engine::$DIR."modules/".$module)){
			throw HandlerException::moduleNotFound($module);
		}
	}

	private function checkRouter():void{
		$module=$this->getModule();
		if(!file_exists(Engine::$DIR."modules/".$module.'/'.$module."Router.php")){    // @todo cambiare con class exists?
			throw HandlerException::routerNotFound($module);
		}
	}

	private function loadRouter():void{
		$module=$this->getModule();
		$routerClass='\Coordinator\Engine\Modules\\'.$module.'\\'.$module.'Router';
		//var_dump($routerClass);
		try{
			$this->Router=new $routerClass;
			//var_dump($this->Router->debugRoutes());
		}catch(\Exception $Exception){
			throw HandlerException::errorLoadingRouter($this->getModule());
		}
	}

	private function checkCallback():void{    // @todo migliorare DRY
		try{
			class_exists($this->Callback->getController());
		}catch(\Exception $Exception){
			throw HandlerException::controllerNotFound($this->getModule(),$this->Callback->getController());
		}
		if(!method_exists($this->Callback->getController(),$this->Callback->getMethod())){
			throw HandlerException::methodNotFound($this->getModule(),$this->Callback->getController(),$this->Callback->getMethod());
		}
	}

	private function loadController():void{
		$controller_class=$this->Callback->getController();
		try{
			$this->Controller=new $controller_class($this->Request,$this->Response);
		}catch(\Exception $Exception){
			throw HandlerException::errorLoadingController($this->getModule(),$this->Callback->getController());
		}
	}

	public function handle():void{
		$method=(isset($this->Callback)?$this->Callback->getMethod():null);
		try{
			if(isset($this->Controller)){
				$this->Controller->$method();
			}
		}catch(\Exception $Exception){
			// if in debug mode show real error
			if(Engine::$DEBUG){throw $Exception;}
			// show generic error for client
			$this->Response->addError(HandlerError::errorExecutingMethod($this->getModule(),$this->Callback->getController(),$this->Callback->getMethod()));
			$this->Response->setCode(Response::RC_500_INTERNAL_SERVER_ERROR);
		}finally{
			$this->log();  // valutare se mettere qui o dentro try
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
	 * Render output
	 */
	public function render():void{
		echo $this->Response->render();
	}



	public function debug():array{
		return array(
		 'module'=>$this->module??null,
		 'command'=>$this->command??null,
		 'Request'=>(isset($this->Request)?$this->Request->debug():null),
		 //'Router'=>$this->Router??null,
		 //'Controller'=>$this->Controller??null,
		 'Callback'=>(isset($this->Callback)?$this->Callback->debug():null),
		 'Response'=>(isset($this->Response)?$this->Response->debug():null)
		);
	}

}