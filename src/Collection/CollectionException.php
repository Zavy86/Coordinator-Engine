<?php
/**
 * Collection Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Collection;

final class CollectionException extends \Exception{

	public const INVALID_COLLECTION_ELEMENT=101;
	public static function invalidCollectionElement(string $type):static{
		return new static('Invalid collection element type, expected '.$type.'.',static::INVALID_COLLECTION_ELEMENT);
	}

}
