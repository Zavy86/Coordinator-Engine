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
			if(is_int($property)){$property=$method;$method='';}
			if(in_array(strtoupper($method),array('ASC','DESC'))){$method=strtoupper($method);}else{$method='ASC';}
			$this->properties[$property]=$method;
		}
	}

	public function getProperties():array{
		return $this->properties;
	}

}
