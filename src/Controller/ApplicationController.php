<?php
/**
 * Application Controller
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Controller;

use Coordinator\Engine\Engine;
use Coordinator\Engine\Response\ResponseCode;
use Coordinator\Engine\Response\ServiceResponse;
use Coordinator\Engine\Response\PingResponse;
use Coordinator\Engine\Response\TimeResponse;
use Coordinator\Engine\Response\DocsResponse;
use Coordinator\Engine\Response\DocsEndpoint;
use Coordinator\Engine\Response\DocsObject;
use Coordinator\Engine\Response\DocsObjectProperty;

final class ApplicationController extends AbstractController{

	#[Description("Retrieve microservice information")]
	#[Response(ServiceResponse::class)]
	public function service(){
		$ServiceResponse=new ServiceResponse();
		$ServiceResponse->version=Engine::$VERSION;
		$ServiceResponse->title=Engine::$TITLE;
		$ServiceResponse->owner=Engine::$OWNER;
		$ServiceResponse->host=$_SERVER['SERVER_ADDR'];
		$ServiceResponse->engine=Engine::$ENGINE;
		$this->Response->setCode(ResponseCode::OK_200);
		$this->Response->setObject($ServiceResponse);
	}

	#[Description("Provide a simple health-check")]
	#[Response(PingResponse::class)]
	public function ping(){
		$PingResponse=new PingResponse();
		$this->Response->setCode(ResponseCode::OK_200);
		$this->Response->setObject($PingResponse);
	}

	#[Description("Retrieve timestamp, datetime and timezone from the microservice")]
	#[Response(TimeResponse::class)]
	public function time(){
		$TimeResponse=new TimeResponse();
		$TimeResponse->timestamp=time();
		$TimeResponse->datetime=date('Y-m-d H:i:s');
		$TimeResponse->timezone=date_default_timezone_get();
		$this->Response->setCode(ResponseCode::OK_200);
		$this->Response->setObject($TimeResponse);
	}

	#[Description("Retrieve a complete microservice endpoints documentation")]
	#[Response(DocsResponse::class)]
	public function docs(){

		$DocsResponse=new DocsResponse();
		$DocsResponse->title=Engine::$TITLE;
		$DocsResponse->version=Engine::$VERSION;

		$routes=Engine::getHandler()->getRouter()->getRoutes();
		//var_dump($routes);

		foreach($routes as $command=>$methods){
			//var_dump($command);
			foreach($methods as $method=>$callback){
				//var_dump($method,$callback);

				$endpoint=new DocsEndpoint();
				$endpoint->method=$method;
				$endpoint->command=$command;

				$reflectionClass=new \ReflectionClass($callback->getController());
				$reflectionMethod=$reflectionClass->getMethod($callback->getFunction());
				$reflectionAttributes=$reflectionMethod->getAttributes();

				foreach($reflectionAttributes as $reflectionAttribute){
					//var_dump($reflectionAttribute->getName());
					//var_dump($reflectionAttribute->getArguments());
					$attributes=['description','request','response'];
					foreach($attributes as $attribute){
						if(str_ends_with(strtolower($reflectionAttribute->getName()),$attribute)){
							if($attribute=='description'){$endpoint->{$attribute}=$reflectionAttribute->getArguments()[0];}
							else{$endpoint->{$attribute}=$this->parseObject($reflectionAttribute->getArguments()[0]);}
						}
					}
				}
				$DocsResponse->endpoints[]=$endpoint;
			}
		}
		$this->Response->setCode(ResponseCode::OK_200);
		$this->Response->setObject($DocsResponse);
	}

	private function parseObject(string $objectClass){

		$object=new DocsObject();
		$object->name=$objectClass;

		$reflectionObject=new \ReflectionClass($objectClass);

		foreach($reflectionObject->getProperties() as $property){

			//var_dump($property->getName());
			//var_dump($property->getType()->getName());
			//var_dump($property->getDocComment());

			$name=$property->getName();
			$typology=$property->getType()->getName();
			$information=null;
			$class=null;

			if($name=='relates'){continue;}

			foreach($property->getAttributes() as $attribute){
				if(str_ends_with(strtolower($attribute->getName()),'information')){
					$information=$attribute->getArguments()[0];
				}
			}

			if(!in_array($typology,['bool','int','float','string','array','object'])){
				$typology='class';
				$class=$this->parseObject($property->getType()->getName());
			}
			if($typology=='array'){
				if(preg_match('/@var\s+([^\s]+)/',$property->getDocComment(),$matches)){
					if(strlen($matches[1])){
						$typology=$matches[1];
						if(class_exists(substr($typology,0,-2))){
							$class=$this->parseObject(substr($typology,0,-2)); // @todo non funziona sempre perche non è sempre il nome completo
						}
					}
				}
			}

			$prop=new DocsObjectProperty();
			$prop->name=$name;
			$prop->typology=$typology;
			$prop->default=json_encode($property->getDefaultValue());
			$prop->information=$information;
			$prop->class=$class;

			$object->properties[]=$prop;
		}
		return $object;
	}

}

