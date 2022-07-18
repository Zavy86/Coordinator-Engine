<?php
/**
 * Callback
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Callback;

use Coordinator\Engine\Controller\ControllerInterface;

final class Callback implements CallbackInterface{

	/**
	 * @param string $controller Controller class name (must implement ControllerInterface)
	 * @param string $function Controller function name
	 */
	final public function __construct(
	 private string $controller,
	 private string $function
	){
		$this->checkController();
		$this->checkInterface();
	}

	final public function getController():string{
		return $this->controller;
	}

	final public function getFunction():string{
		return $this->function;
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
		 'function'=>$this->getFunction()
		);
	}

}