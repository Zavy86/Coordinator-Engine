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
use Coordinator\Engine\Model\ModelInterface;
use Coordinator\Engine\Object\BooleanObject;
use Coordinator\Engine\Object\BrowseObject;
use Coordinator\Engine\Object\CreateObject;
use Coordinator\Engine\Pagination\Pagination;
use Coordinator\Engine\Request\RequestInterface;
use Coordinator\Engine\Response\ResponseInterface;
use Coordinator\Engine\Response\ResponseCode;

abstract class AbstractController implements ControllerInterface{

	public function __construct(
		protected RequestInterface $Request,
		protected ResponseInterface $Response
	){}

	// @todo implementare
	protected function check(string $authorization=null):bool{
		$authorized=true;
		if(!$this->checkSessionValidity()){
			$authorized=false;
			$this->Response->addError((new Error("authenticationInvalid",'Authentication token provided is not valid')));
		}
		if(!is_null($authorization) && strlen($authorization) && !$this->checkAuthorization($authorization)){
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

	protected function checkAuthorization(string $authorization):bool{  // @todo fare classe specifica?
		if(!strlen($authorization)){return true;}
		$Session=Engine::getSession();
		if(!$Session->isValid()){return false;}
		return (in_array($authorization,$Session->getAuthorizations()));
	}

	protected function checkModelExists(string $modelClass,string $uid):bool{
		if(!$this->checkInterface($modelClass,ModelInterface::class)){
			throw new \Exception('ModelInterface not implemented by '.$modelClass);
		}
		if(!$modelClass::exists($uid)){
			$this->Response->setCode(ResponseCode::NOT_FOUND_404);
			$this->Response->addError(new Error('notFound',$uid.' was not found in class '.$modelClass.'.'));
			return false;
		}
		return true;
	}

	public function debug():array{
		return array(
			'class'=>$this::class
			//'Request'=>$this->Request->debug(),
			//'Response'=>$this->Request->debug()
		);
	}

	/** @todo fare function perche questa roba serve un po' ovunque */
	protected function checkInterface(string $class,string $interface):bool{
		$interfaces=class_implements($class);
		if(is_array($interfaces) && in_array($interface,$interfaces)){return true;}
		return false;
	}

	protected function _browse(string $modelClass):void{
		if(!$this->checkInterface($modelClass,ModelInterface::class)){
			throw new \Exception('ModelInterface not implemented by '.$modelClass);
		}

		$Response=new BrowseObject();

		// @todo implementare filters, sorting

		$Pagination=Pagination::fromRequestQuery(20,100);
		//var_dump($Pagination);

		/** @var ModelInterface $modelClass */
		$Response->count=$modelClass::count();
		$Response->limit=$Pagination->getLimit();
		$Response->offset=$Pagination->getOffset();
		$Response->uids=$modelClass::browse(null,null,$Pagination);
		//var_dump($Response);
		$this->Response->setObject($Response);
	}

	protected function _load(string $modelClass):void{

		if(!$this->checkInterface($modelClass,ModelInterface::class)){
			throw new \Exception('ModelInterface not implemented by '.$modelClass);
		}

		// @todo capire come estrarlo tramite preg_match
		$uri_exploded=explode("/",$this->Request->getUri());
		$uid=end($uri_exploded);
		//var_dump($uid);
		/** @var ModelInterface $modelClass */
		if(!$modelClass::exists($uid)){
			$this->Response->setCode(ResponseCode::NOT_FOUND_404);
			$this->Response->addError(new Error('notFound',$uid.' was not found.'));
			return;
		}
		$Model=$modelClass::load($uid);
		//var_dump($Model);
		$this->Response->setObject($Model);
		//var_dump($this->Response->debug());
	}

	protected function _create(string $modelClass):void{

		if(!$this->checkInterface($modelClass,ModelInterface::class)){
			throw new \Exception('ModelInterface not implemented by '.$modelClass);
		}

		try{
			$Model=new $modelClass();
			$Model->setProperties($this->Request->getBody());
			//var_dump($Model);
			if(!$Model->save()){
				throw new \Exception('not created');
			}
			$Response=new CreateObject(['uid'=>$Model->getUid()]);
			//var_dump($Response);
			$this->Response->setObject($Response);
		}catch(\Exception|\TypeError $Exception){
			// @todo fare le varie prove e catchare chiavi duplicate univoci duplicati ecc...
			//var_dump($Exception);
			$this->Response->setCode(ResponseCode::BAD_REQUEST_400);
			$this->Response->addError(new Error('errorCreate','Error creating.'));
		}
	}

	protected function _update(string $modelClass):void{

		if(!$this->checkInterface($modelClass,ModelInterface::class)){
			throw new \Exception('ModelInterface not implemented by '.$modelClass);
		}

		try{
			// @todo capire come estrarlo tramite preg_match
			$uri_exploded=explode("/",$this->Request->getUri());
			$uid=end($uri_exploded);
			//var_dump($uid);
			if(!$modelClass::exists($uid)){
				$this->Response->setCode(ResponseCode::NOT_FOUND_404);
				$this->Response->addError(new Error('notFound',$uid.' was not found.'));
				return;
			}
			$Model=$modelClass::load($uid);
			//var_dump($Model);
			$Model->setProperties($this->Request->getBody());
			//var_dump($Model);
			if(!$Model->save()){throw new \Exception('not updated');}
			$Response=BooleanObject::true();
			//var_dump($Response);
			$this->Response->setObject($Response);
		}catch(\Exception|\TypeError $Exception){
			// @todo fare le varie prove e catchare chiavi duplicate univoci duplicati ecc...
			//var_dump($Exception);
			$this->Response->setCode(ResponseCode::BAD_REQUEST_400);
			$this->Response->addError(new Error('system.accounts.error','Error saving System Model.'));
		}
	}

	protected function _remove(string $modelClass):void{

		if(!$this->checkInterface($modelClass,ModelInterface::class)){
			throw new \Exception('ModelInterface not implemented by '.$modelClass);
		}

		try{
			// @todo capire come estrarlo tramite preg_match
			$uri_exploded=explode("/",$this->Request->getUri());
			$uid=end($uri_exploded);
			//var_dump($uid);
			if(!$modelClass::exists($uid)){
				$this->Response->setCode(ResponseCode::NOT_FOUND_404);
				$this->Response->addError(new Error('notFound',$uid.' was not found.'));
				return;
			}
			$Model=$modelClass::load($uid);
			//var_dump($Model);
			if(!$Model->remove()){throw new \Exception('not deleted');}
			$Response=BooleanObject::true();
			//var_dump($Response);
			$this->Response->setObject($Response);
		}catch(\Exception|\TypeError $Exception){
			// @todo fare le varie prove e catchare chiavi duplicate univoci duplicati ecc...
			//var_dump($Exception);
			$this->Response->setCode(ResponseCode::BAD_REQUEST_400);
			$this->Response->addError(new Error('system.accounts.error','Error deleting System Model.'));
		}
	}

}
