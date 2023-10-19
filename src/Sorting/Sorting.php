<?php
/**
 * Sorting
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Sorting;

class Sorting implements SortingInterface{

	protected array $properties=[];

	/**
	 * @param array $properties Key property name value ASC or DESC
	 */
	public function __construct(array $properties){
		foreach($properties as $property=>$method){
			if(is_int($property)){$property=$method;$method='ASC';}
			else{$method=strtoupper($method);}
			if(!in_array($method,['ASC','DESC'])){throw SortingException::methodInvalid($method);}
			$this->properties[$property]=$method;
		}
	}

	public function getProperties():array{
		return $this->properties;
	}

	public function getRaw():?array{
		$return=[];
		if(!count($this->properties)){return null;}
		foreach($this->properties as $property=>$method){
			array_push($return,['property'=>$property,'method'=>$method]);
		}
		return $return;
	}

}
