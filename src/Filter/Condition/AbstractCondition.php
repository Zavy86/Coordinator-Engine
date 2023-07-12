<?php
/**
 * Abstract Condition
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter\Condition;

abstract class AbstractCondition implements ConditionInterface{

	protected string $assertion;
	protected string $property;
	protected mixed $value;

	public function __construct(string $property,mixed $value){
		$this->assertion=substr(strrchr($this::class,"\\"),1);
		$this->property=$property;
		$this->value=$value;
	}

	public function getAssertion():string{return $this->assertion;}
	public function getProperty():string{return $this->property;}
	public function getValue():mixed{return $this->value;}

	public function returnTextual(int $depth=0):string{
		if(is_array($this->getValue())){$value_string=implode(', ',$this->getValue());}
		elseif(is_null($this->getValue())){$value_string=null;}
		else{$value_string=$this->getValue();}
		if(!is_null($value_string)){$value_string=" '".$value_string."'";}
		return str_repeat(' ',$depth).$this->property.'" '.$this->assertion.$value_string.PHP_EOL;
	}

	public function debug():array{ // @todo?
		return array(
			'class'=>$this::class
		);
	}

}
