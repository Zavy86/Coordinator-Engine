<?php
/**
 * Conditions
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Filter\Condition;

use Coordinator\Engine\Filter\FilterException;

class Conditions{

	protected string $operator;
	protected array $conditions=[];

	public function __construct(string $operator,Conditions|ConditionInterface...$conditionFilters){
		$this->setOperator($operator);
		foreach($conditionFilters as $conditionFilter){
			$this->addCondition($conditionFilter);
		}
	}

	public function setOperator(string $operator){
		if(!in_array(trim(strtoupper($operator)),array('AND','OR'))){throw FilterException::conditionsOperatorInvalid($operator);}
		$this->operator=trim(strtoupper($operator));
	}

	public function addCondition(Conditions|ConditionInterface $conditionFilter){
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
