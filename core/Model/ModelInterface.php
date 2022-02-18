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

	public static function count(?FilterInterface $Filters=null):int;

	public static function browse(?FilterInterface $Filters=null,?SortingInterface $Sorting=null,?PaginationInterface $Pagination=null):array;

	public static function exists(mixed $uid):bool;

	public static function load(mixed $uid):static;

	public function save():bool;

	public function remove():bool;

	public function getProperties():array;

	public function setProperty(string $property,mixed $value):bool;

	public function debug():array;

	// @todo   hasProperty($property)    getProperty($property)

}