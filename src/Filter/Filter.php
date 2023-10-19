<?php
/**
 * Filter
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter;

use Coordinator\Engine\Filter\Condition\ConditionInterface;
use Coordinator\Engine\Filter\Condition\isBetween;
use Coordinator\Engine\Filter\Condition\isEqualsTo;
use Coordinator\Engine\Filter\Condition\isGreaterEqualsThan;
use Coordinator\Engine\Filter\Condition\isGreaterThan;
use Coordinator\Engine\Filter\Condition\isIn;
use Coordinator\Engine\Filter\Condition\isLesserEqualsThan;
use Coordinator\Engine\Filter\Condition\isLesserThan;
use Coordinator\Engine\Filter\Condition\isLike;
use Coordinator\Engine\Filter\Condition\isNotBetween;
use Coordinator\Engine\Filter\Condition\isNotEqualsTo;
use Coordinator\Engine\Filter\Condition\isNotIn;
use Coordinator\Engine\Filter\Condition\isNotLike;
use Coordinator\Engine\Filter\Condition\isNotNull;
use Coordinator\Engine\Filter\Condition\isNull;

class Filter implements FilterInterface{

	public function __construct(
		protected FilterConditions|ConditionInterface $condition
	){}

	public function debug():array{ // @todo?
		return array(
			'class'=>$this::class
		);
	}

	public function getCondition():FilterConditions|ConditionInterface{
		return $this->condition;
	}

	public function getRaw():?array{
		return $this->condition->getRaw();
	}

	public function returnTextual():string{
		return PHP_EOL.'FILTER STRING:'.PHP_EOL.$this->condition->returnTextual(0);
	}

	public static function buildFromArray(array $properties):?Filter{
		if(array_key_exists('assertion',$properties)){return new Filter(self::buildCondition($properties));}
		elseif(array_key_exists('operator',$properties)){return new Filter(self::buildConditions($properties));}
		else{throw FilterException::parsingError();}
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
		return new FilterConditions($properties['operator'],...$Conditions);
	}

	private static function buildCondition(array $properties):ConditionInterface{
		if(!array_key_exists('assertion',$properties) || !array_key_exists('property',$properties)){throw FilterException::parsingError();}
		$class='Coordinator\\Engine\\Filter\\Condition\\'.$properties['assertion'];
		if(!class_exists($class)){throw FilterException::conditionInvalid($properties['assertion']);}
		switch($properties['assertion']){
			case 'isNull':return new isNull($properties['property']);
			case 'isNotNull':return new isNotNull($properties['property']);
			case 'isEqualsTo':return new isEqualsTo($properties['property'],$properties['value']);
			case 'isNotEqualsTo':return new isNotEqualsTo($properties['property'],$properties['value']);
			case 'isGreaterThan':return new isGreaterThan($properties['property'],$properties['value']);
			case 'isGreaterEqualsThan':return new isGreaterEqualsThan($properties['property'],$properties['value']);
			case 'isLesserThan':return new isLesserThan($properties['property'],$properties['value']);
			case 'isLesserEqualsThan':return new isLesserEqualsThan($properties['property'],$properties['value']);
			case 'isNotLike':return new isNotLike($properties['property'],$properties['value']);
			case 'isLike':return new isLike($properties['property'],$properties['value']);
			case 'isNotIn':return new isNotIn($properties['property'],$properties['value']);
			case 'isIn':return new isIn($properties['property'],$properties['value']);
			case 'isNotBetween':return new isNotBetween($properties['property'],$properties['value'][0],$properties['value'][1]);
			case 'isBetween':return new isBetween($properties['property'],$properties['value'][0],$properties['value'][1]);
			default:throw FilterException::conditionInvalid($properties['assertion']);
		}
	}

}
