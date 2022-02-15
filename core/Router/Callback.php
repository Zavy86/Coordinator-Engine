<?php
/**
 * Router Callback
 *
 * @package Coordinator\Engine\Router
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Router;

use Coordinator\Engine\Controller\ControllerInterface;

final class Callback implements CallbackInterface{

	/**
	 * @param string $controller Controller class name (must implement ControllerInterface)
	 * @param string $method Controller method name
	 */
	final public function __construct(
	 private string $controller,
	 private string $method
	){
		$this->checkController();
		$this->checkInterface();
	}

	final public function getController():string{
		return $this->controller;
	}

	final public function getMethod():string{
		return $this->method;
	}

	/** @todo valutare se fare qui o se fare in handler */

	private function checkController():void{
		try{
			class_exists($this->getController());
		}catch(\Exception $Exception){
			throw CallbackException::controllerNotFound($this->getController());
		}
	}

	private function checkInterface():void{
		$interfaces=class_implements($this->getController());
		if(!is_array($interfaces) || !in_array(ControllerInterface::class,$interfaces)){
			throw CallbackException::controllerTypeMismatch($this->getController());
		}
	}

	public function debug():array{
		return array(
		 'class'=>$this::class,
		 'controller'=>$this->getController(),
		 'method'=>$this->getMethod()
		);
	}

}