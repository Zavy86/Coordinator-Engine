<?php
/**
 * Abstract Error
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Error;

abstract class AbstractError implements ErrorInterface{

	/**
	 * Properties
	 */
	protected string $code;
	protected ?string $description=null;
	protected ?string $information=null;

	/** @inheritDoc */
	final public function __construct(string $code,?string $description=null,?string $information=null){
		$this->setCode($code);
		$this->setDescription($description);
		$this->setInformation($information);
	}

	final public function output():array{
		return array(
		 'code'=>$this->getCode(),
		 'description'=>$this->getDescription(),
		 'information'=>$this->getInformation(),
		);
	}

	final public function getCode():string{return $this->code;}
	final public function getDescription():?string{return $this->description;}
	final public function getInformation():?string{return $this->information;}

	/**
	 * Set Code
	 * @param string $code Error code
	 * @return bool
	 */
	final public function setCode(string $code):bool{
		if(0){return false;}                           /* @todo check */
		$this->code=trim($code);
		return true;
	}

	/**
	 * Set Description
	 * @param ?string $description Error description
	 * @return bool
	 */
	final public function setDescription(?string $description=null):bool{
		if(0){return false;}                           /* @todo check */
		if(is_null($description) || !strlen(trim($description))){return false;}
		$this->description=trim($description);
		return true;
	}

	/**
	 * Set Information
	 * @param ?string $information Error information
	 * @return bool
	 */
	final public function setInformation(?string $information=null):bool{
		if(0){return false;}                           /* @todo check */
		if(is_null($information) || !strlen(trim($information))){return false;}
		$this->information=trim($information);
		return true;
	}

}
