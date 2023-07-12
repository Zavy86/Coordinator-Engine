<?php
/**
 * Filter
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter;

use Coordinator\Engine\Filter\Condition\ConditionInterface;
use Coordinator\Engine\Filter\Condition\ConditionIsBetween;
use Coordinator\Engine\Filter\Condition\ConditionIsEqualsTo;
use Coordinator\Engine\Filter\Condition\ConditionIsGreaterEqualsThan;
use Coordinator\Engine\Filter\Condition\ConditionIsGreaterThan;
use Coordinator\Engine\Filter\Condition\ConditionIsIn;
use Coordinator\Engine\Filter\Condition\ConditionIsLesserEqualsThan;
use Coordinator\Engine\Filter\Condition\ConditionIsLesserThan;
use Coordinator\Engine\Filter\Condition\ConditionIsLike;
use Coordinator\Engine\Filter\Condition\ConditionIsNotBetween;
use Coordinator\Engine\Filter\Condition\ConditionIsNotEqualsTo;
use Coordinator\Engine\Filter\Condition\ConditionIsNotIn;
use Coordinator\Engine\Filter\Condition\ConditionIsNotLike;
use Coordinator\Engine\Filter\Condition\ConditionIsNotNull;
use Coordinator\Engine\Filter\Condition\ConditionIsNull;

class Filter implements FilterInterface{

	public function __construct(
		protected FilterConditions|ConditionInterface $condition
	){}

	public function returnTextual():string{
		return PHP_EOL.'FILTER STRING:'.PHP_EOL.$this->condition->returnTextual(0);
	}

	public function getCondition():FilterConditions|ConditionInterface{
		return $this->condition;
	}

	public function debug():array{ // @todo?
		return array(
			'class'=>$this::class
		);
	}

	public static function buildFromArray(array $properties):?Filter{
		if(array_key_exists('assertion',$properties)){return new Filter(self::buildCondition($properties));}
		elseif(array_key_exists('operator',$properties)){return new Filter(self::buildConditions($properties));}
		else{throw FilterException::parsingError();}
	}

	private static function buildCondition(array $properties):ConditionInterface{
		if(!array_key_exists('assertion',$properties) || !array_key_exists('property',$properties)){throw FilterException::parsingError();}
		$class='Coordinator\\Engine\\Filter\\Condition\\Condition'.ucfirst($properties['assertion']);
		if(!class_exists($class)){throw FilterException::conditionInvalid($properties['assertion']);}
		switch($properties['assertion']){
			case 'isNull':return new ConditionIsNull($properties['property']);
			case 'isNotNull':return new ConditionIsNotNull($properties['property']);
			case 'isEqualsTo':return new ConditionIsEqualsTo($properties['property'],$properties['value']);
			case 'isNotEqualsTo':return new ConditionIsNotEqualsTo($properties['property'],$properties['value']);
			case 'isGreaterThan':return new ConditionIsGreaterThan($properties['property'],$properties['value']);
			case 'isGreaterEqualsThan':return new ConditionIsGreaterEqualsThan($properties['property'],$properties['value']);
			case 'isLesserThan':return new ConditionIsLesserThan($properties['property'],$properties['value']);
			case 'isLesserEqualsThan':return new ConditionIsLesserEqualsThan($properties['property'],$properties['value']);
			case 'isNotLike':return new ConditionIsNotLike($properties['property'],$properties['value']);
			case 'isLike':return new ConditionIsLike($properties['property'],$properties['value']);
			case 'isNotIn':return new ConditionIsNotIn($properties['property'],$properties['value']);
			case 'isIn':return new ConditionIsIn($properties['property'],$properties['value']);
			case 'isNotBetween':return new ConditionIsNotBetween($properties['property'],$properties['value'][0],$properties['value'][1]);
			case 'isBetween':return new ConditionIsBetween($properties['property'],$properties['value'][0],$properties['value'][1]);
			default:throw FilterException::conditionInvalid($properties['assertion']);
		}
	}

	private static function buildConditions(array $properties):FilterConditions{
		if(!array_key_exists('operator',$properties) || !array_key_exists('Conditions',$properties)){throw FilterException::parsingError();}
		$Conditions=[];
		foreach($properties['Conditions'] as $Condition_properties){
			if(array_key_exists('assertion',$Condition_properties)){
				$Conditions[]=self::buildCondition($Condition_properties);
			}elseif(array_key_exists('operator',$Condition_properties)){
				$Conditions[]=self::buildConditions($Condition_properties);
			}else{
				throw FilterException::parsingError();
			}
		}
		$operator=Operator::tryFrom($properties['operator']) ?? throw FilterException::conditionsOperatorInvalid($properties['operator']);
		return new FilterConditions($operator,...$Conditions);
	}

}
