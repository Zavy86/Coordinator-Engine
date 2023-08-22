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
				$endpoint->description=null;
				$endpoint->query=new DocsObject('Customizable String');
				$endpoint->request=new DocsObject('Customizable Object');

				$reflectionClass=new \ReflectionClass($callback->getController());
				$reflectionMethod=$reflectionClass->getMethod($callback->getFunction());
				$reflectionAttributes=$reflectionMethod->getAttributes();

				foreach($reflectionAttributes as $reflectionAttribute){
					//var_dump($reflectionAttribute->getName());
					//var_dump($reflectionAttribute->getArguments());

					if(str_ends_with(strtolower($reflectionAttribute->getName()),'description')){
						$endpoint->description=$reflectionAttribute->getArguments()[0];
					}

					if(str_ends_with(strtolower($reflectionAttribute->getName()),'search')){
						$endpoint->query->properties[]=new DocsObjectProperty('search','string','null','you can use *');
					}

					if(str_ends_with(strtolower($reflectionAttribute->getName()),'pagination')){
						$endpoint->query->properties[]=new DocsObjectProperty('limit','int','20','pagination limit');
						$endpoint->query->properties[]=new DocsObjectProperty('offset','int','0','pagination offset');
					}

					if(str_ends_with(strtolower($reflectionAttribute->getName()),'query')){
						$arguments=$reflectionAttribute->getArguments();
						if(array_key_exists('name',$arguments)){
							if(array_key_exists('typology',$arguments)){$typology=$arguments['typology'];}else{$typology='string';}
							if(array_key_exists('default',$arguments)){$default='"'.$arguments['default'].'"';}else{$default='null';}
							if(array_key_exists('information',$arguments)){$information='"'.$arguments['information'].'"';}else{$information=null;}
							$endpoint->query->properties[]=new DocsObjectProperty($arguments['name'],$typology,$default,$information);
						}
					}

					if(str_ends_with(strtolower($reflectionAttribute->getName()),'filter')){
						$endpoint->request->properties[]=new DocsObjectProperty('Filter','Conditions','null',null,
							new DocsObject('sort',[
								new DocsObjectProperty('operator','string','null','or | and'),
								new DocsObjectProperty('Conditions','Condition[] | Conditions','null',null,
									new DocsObject('array',[
										new DocsObjectProperty('assertion','string','null','isNull | isNotNull | isEqualsTo | isNotEqualsTo | isGreaterThan | isGreaterEqualsThan | isLesserThan | isLesserEqualsThan | isLike | isNotLike | isIn | isNotIn | isBetween | isNotBetween'),
										new DocsObjectProperty('property','string','null'),
										new DocsObjectProperty('value','mixed','null')
									])
								)
							])
						);
					}

					if(str_ends_with(strtolower($reflectionAttribute->getName()),'sorting')){
						$endpoint->request->properties[]=new DocsObjectProperty('Sorting','Sorting[]','null',null,
							new DocsObject('sort',[
								new DocsObjectProperty('property','string'),
								new DocsObjectProperty('method','string','"asc"','asc | desc')
							])
						);
					}

					$attributes=['request','response'];
					foreach($attributes as $attribute){
						if(str_ends_with(strtolower($reflectionAttribute->getName()),$attribute)){
							$endpoint->{$attribute}=$this->parseObject($reflectionAttribute->getArguments()[0]);
						}
					}
				}

				if(!count($endpoint->query->properties)){$endpoint->query=null;}
				if(!count($endpoint->request->properties)){$endpoint->request=null;}

				$DocsResponse->endpoints[]=$endpoint;
			}
		}
		$this->Response->setCode(ResponseCode::OK_200);
		$this->Response->setObject($DocsResponse);
	}

	private function parseObject(string $objectClass,$array=false){

		$object=new DocsObject($objectClass);

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

			if(!in_array($typology,['bool','int','float','string','array','object','mixed'])){
				$typology='class';
				$class=$this->parseObject($property->getType()->getName());
			}

			if($typology=='array'){
				if(preg_match('/@var\s+([^\s]+)/',$property->getDocComment(),$matches)){
					if(strlen($matches[1])){
						$typology=substr($matches[1],0,-2);
						if(!str_starts_with($typology,'\\')){$typology=$reflectionObject->getNamespaceName().'\\'.$typology;}
						if(str_starts_with($typology,'\\')){$typology=substr($typology,1);}
						//var_dump($typology);
						if($typology!=$object->name && class_exists($typology)){
							$class=$this->parseObject($typology,true); // @todo non funziona sempre perche non è sempre il nome completo
						}
					}
				}
				if($typology==$object->name){$typology.='[] (recursive)';}else{$typology.='[]';}
			}

			if($property->getType()->allowsNull()){$typology.=' | null';}

			$property=new DocsObjectProperty($name,$typology,json_encode($property->getDefaultValue()),$information,$class);

			$object->properties[]=$property;
		}
		// add array notation
		$object->name.=($array?'[]':null);
		return $object;
	}

}
