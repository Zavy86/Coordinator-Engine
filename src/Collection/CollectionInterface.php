<?php
/**
 * Collection Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Collection;

interface CollectionInterface{

	//public function __construct(...$elements);

	public function getElementType():string;

}
