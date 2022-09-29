<?php
/**
 * Abstract Router
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Router;

use Coordinator\Engine\Controller\ApplicationController;
use Coordinator\Engine\Callback\CallbackInterface;
use Coordinator\Engine\Callback\Callback;

abstract class AbstractRouter implements RouterInterface{

	/** @var CallbackInterface[][] Callback Routes with keys [command][method] */
	protected array $routes=[];

	final public function __construct(){
		// add application routes
		$this->GET('/^\/Service$/',(new Callback(ApplicationController::class,'service')));
		$this->GET('/^\/Ping$/',(new Callback(ApplicationController::class,'ping')));
		$this->GET('/^\/Time$/',(new Callback(ApplicationController::class,'time')));
		// load application routes
		$this->loadRoutes();
	}

	final public function GET(string $command,CallbackInterface $callback):void{  // @todo mettere anche nell'interfaccia?
		$this->addRoute("GET",$command,$callback);
	}

	final public function POST(string $command,CallbackInterface $callback):void{
		$this->addRoute("POST",$command,$callback);
	}

	final public function PUT(string $command,CallbackInterface $callback):void{
		$this->addRoute("PUT",$command,$callback);
	}

	final public function DELETE(string $command,CallbackInterface $callback):void{
		$this->addRoute("DELETE",$command,$callback);
	}

	final public function addRoute(string $method,string $command,CallbackInterface $callback):void{
		$this->routes[$command][strtoupper($method)]=$callback;
	}

	final public function resolveRoute(string $requestMethod,string $requestCommand):CallbackInterface{
		$resolved_callback=null;
		$cleanedMethod=strtoupper($requestMethod);
		$cleanedCommand=(str_ends_with($requestCommand,"/")?substr($requestCommand,0,-1):$requestCommand);
		foreach($this->routes as $command=>$methods){
			if(preg_match($command,$cleanedCommand)){
				if(!array_key_exists($cleanedMethod,$methods)){
					throw RouterException::methodNotAllowed(static::class,$cleanedCommand,$cleanedMethod);
				}
				$resolved_callback=$methods[$cleanedMethod];
				break;
			}
		}
		if(is_null($resolved_callback)){
			throw RouterException::routeNotResolved(static::class,$cleanedCommand,$cleanedMethod);  // callback instead of command
		}
		return $resolved_callback;
	}

	final public function debug():array{
		return array(
			'class'=>$this::class
			//'routes'=>$this->routes
		);
	}

}
