<?php
/**
 * Abstract Collection
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Collection;

abstract class AbstractCollection extends \ArrayObject implements CollectionInterface{

	static protected string $type;

	private function checkElementType($element){
		if($element instanceof static::$type===false){
			throw CollectionException::invalidCollectionElement($this->elementType);
		}
	}

	public function __construct(...$elements){
		foreach($elements as $element){$this->checkElementType($element);}
		parent::__construct($elements);
	}

	public function append($element):void{
		$this->checkElementType($element);
		parent::append($element);
	}

	public function offsetSet($index,$element):void{
		$this->checkElementType($element);
		parent::offsetSet($index,$element);
	}

	public static function getType():string{
		return static::$type;
	}

}
