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

	private static function checkSessionValidity():bool{
		$Session=Engine::getSession();
		//var_dump($Session);
		return $Session->isValid();
	}

	protected static function checkAuthorization(string $authorization):bool{  // @todo fare classe specifica?
		if(!strlen($authorization)){return false;}
		$Session=Engine::getSession();
		if(!$Session->isValid()){return false;}
		if($Session->isAdministrator()){return true;}  // @todo valutare se tenere o se parametrizzare
		return (in_array($authorization,$Session->getAuthorizations()));
	}

	protected static function checkAuthorizations(array $authorizations):bool{
		foreach($authorizations as $authorization){if(self::checkAuthorization($authorization)){return true;}}
		return false;
	}

	// @todo migliorare?
	protected function check(array|string|null $authorization=null):bool{
		if(!is_array($authorization)){$authorization=array($authorization);}
		$authorization_array=array_filter($authorization);
		//var_dump($authorization_array);
		if($this->checkSessionValidity()){
			if(!count($authorization_array)){
				return true;
			}else{
				foreach($authorization_array as $authorization){
					if($this->checkAuthorization($authorization)){
						return true;
					}
				}
				$this->Response->addError((new Error("authorizationDenied",'You have not the authorization to perform this operation')));
			}
		}else{
			$this->Response->addError((new Error("authenticationInvalid",'Authentication token provided is not valid')));
		}
		return false;
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

}
