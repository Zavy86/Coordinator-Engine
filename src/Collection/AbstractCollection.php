<?php
/**
 * Abstract Collection
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Collection;

abstract class AbstractCollection extends \ArrayObject implements CollectionInterface{

	private string $elementType;

	private function checkElementType(mixed $element):void{
		if($element instanceof $this->elementType===false){
			throw CollectionException::invalidCollectionElement($this->elementType);
		}
	}

	public function __construct(string $elementType,...$elements){
		$this->elementType = $elementType;
		foreach($elements as $element){$this->checkElementType($element);}
		parent::__construct($elements);
	}

	public function append(mixed $element):void{
		$this->checkElementType($element);
		parent::append($element);
	}

	public function offsetSet($index,$element):void{
		$this->checkElementType($element);
		parent::offsetSet($index,$element);
	}

	public function getElementType():string{
		return $this->elementType;
	}

}
