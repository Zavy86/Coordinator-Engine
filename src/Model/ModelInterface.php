<?php
/**
 * Model Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Model;

use Coordinator\Engine\Filter\FilterInterface;
use Coordinator\Engine\Sorting\SortingInterface;
use Coordinator\Engine\Pagination\PaginationInterface;

interface ModelInterface{

	public static function count(?FilterInterface $Filter=null):int;

	public static function browse(?FilterInterface $Filter=null,?SortingInterface $Sorting=null,?PaginationInterface $Pagination=null):array;

	public static function exists(mixed $uid):bool;

	public static function load(mixed $uid):static;

	public static function build(array $properties):static;

	public function save():bool;

	public function remove():bool;

	public function getUid():mixed;

	public function getProperties():array;

	public function setProperty(string $property,mixed $value):bool;

	public function hasEvents():bool;

	public function getEvents():array;

	public function addEvent(string $event,mixed $data=null):void;

	public function clearEvent():void;

	public function debug():array;

	// @todo   hasProperty($property)

}
