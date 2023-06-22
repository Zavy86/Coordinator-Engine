<?php
/**
 * Filter
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter;

use Coordinator\Engine\Request\RequestException;

class Filter implements FilterInterface{

	public function __construct(
		protected Conditions|Condition $condition
	){}

	public function returnTextual():string{
		return PHP_EOL.'FILTER STRING:'.PHP_EOL.$this->condition->returnTextual(0);
	}

	public function getCondition():Conditions|Condition{
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

	private static function buildCondition(array $properties):Condition{
		if(!array_key_exists('assertion',$properties) || !array_key_exists('property',$properties)){throw FilterException::parsingError();}
		$class='Coordinator\\Engine\\Filter\\'.$properties['assertion'];
		if(!class_exists($class)){throw FilterException::conditionInvalid($properties['assertion']);}
		switch($properties['assertion']){
			case 'isNull':return new isNull($properties['property']);
			case 'isNotNull':return new isNotNull($properties['property']);
			case 'isEqualsTo':return new isEqualsTo($properties['property'],$properties['value']);
			case 'isNotEqualsTo':return new isNotEqualsTo($properties['property'],$properties['value']);
			case 'isGreaterThan':return new isGreaterThan($properties['property'],$properties['value']);
			case 'isGreaterEqualThan':return new isGreaterEqualThan($properties['property'],$properties['value']);
			case 'isLesserThan':return new isLesserThan($properties['property'],$properties['value']);
			case 'isLesserEqualThan':return new isLesserEqualThan($properties['property'],$properties['value']);
			case 'isNotLike':return new isNotLike($properties['property'],$properties['value']);
			case 'isLike':return new isLike($properties['property'],$properties['value']);
			case 'isNotIn':return new isNotIn($properties['property'],$properties['value']);
			case 'isIn':return new isIn($properties['property'],$properties['value']);
			case 'isNotBetween':return new isNotBetween($properties['property'],$properties['value'][0],$properties['value'][1]);
			case 'isBetween':return new isBetween($properties['property'],$properties['value'][0],$properties['value'][1]);
			default:throw FilterException::conditionInvalid($properties['assertion']);
		}
	}

	private static function buildConditions(array $properties):Conditions{
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
		return new Conditions($properties['operator'],...$Conditions);
	}

}


class Conditions{

	protected string $operator;
	protected array $conditions=[];

	public function __construct(string $operator,Conditions|Condition...$conditionFilters){
		$this->setOperator($operator);
		foreach($conditionFilters as $conditionFilter){
			$this->addCondition($conditionFilter);
		}
	}

	public function setOperator(string $operator){
		if(!in_array(trim(strtoupper($operator)),array('AND','OR'))){throw FilterException::conditionsOperatorInvalid($operator);}
		$this->operator=trim(strtoupper($operator));
	}

	public function addCondition(Conditions|Condition $conditionFilter){
		$this->conditions[]=$conditionFilter;
	}

	public function getOperator():string{return $this->operator;}
	public function getConditions():array{return $this->conditions;}

	public function returnTextual(int $depth=0){
		$return_string=array();
		foreach($this->getConditions() as $condition){
			$return_string[]=$condition->returnTextual(($depth+1));
		}
		return str_repeat(' ',$depth).'('.PHP_EOL.implode(str_repeat(' ',($depth+1)).$this->getOperator().PHP_EOL,$return_string).str_repeat(' ',$depth).')'.PHP_EOL;
	}

}


abstract class Condition{

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
		return str_repeat(' ',$depth).'"'.$this->property.'" '.$this->assertion.$value_string.PHP_EOL;
	}

}


abstract class NullValueCondition extends Condition{public function __construct(string $property){parent::__construct($property);}}
abstract class SingleValueCondition extends Condition{public function __construct(string $property,bool|int|float|string $value){parent::__construct($property,$value);}}
abstract class MultipleValuesCondition extends Condition{public function __construct(string $property,array $value){parent::__construct($property,$value);}}
abstract class RangeValuesCondition extends Condition{public function __construct(string $property,int|float|string $value_from,int|float|string $value_to){parent::__construct($property,[$value_from,$value_to]);}}


class isNull extends NullValueCondition{}
class isNotNull extends NullValueCondition{}
class isEqualsTo extends SingleValueCondition{}
class isNotEqualsTo extends SingleValueCondition{}
class isGreaterThan extends SingleValueCondition{}
class isGreaterEqualThan extends SingleValueCondition{}
class isLesserThan extends SingleValueCondition{}
class isLesserEqualThan extends SingleValueCondition{}
class isLike extends SingleValueCondition{}
class isNotLike extends SingleValueCondition{}
class isIn extends MultipleValuesCondition{}
class isNotIn extends MultipleValuesCondition{}
class isBetween extends RangeValuesCondition{}
class isNotBetween extends RangeValuesCondition{}
