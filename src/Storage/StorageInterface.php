<?php
/**
 * Storage Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Storage;

use Coordinator\Engine\Model\ModelInterface;
use Coordinator\Engine\Filter\FilterInterface;
use Coordinator\Engine\Sorting\SortingInterface;
use Coordinator\Engine\Pagination\PaginationInterface;

interface StorageInterface{

	public function count(ModelInterface $Model,?FilterInterface $Filters=null):int;

	public function browse(ModelInterface $Model,?FilterInterface $Filters=null,?SortingInterface $Sorting=null,?PaginationInterface $Pagination=null):array;

	public function exists(ModelInterface $Model,mixed $uid):bool;

	public function load(ModelInterface $Model,mixed $uid):ModelInterface;

	public function loadFromKey(ModelInterface $Model,string $key,mixed $value,mixed &$uid):ModelInterface;   // al momento è usato solo per mysql capire se possibile usare browse
	public function loadFromKeys(ModelInterface $Model,array $key,mixed &$uid):ModelInterface;                // vedi sopra

	public function save(ModelInterface $Model):bool;

	public function remove(ModelInterface $Model):bool;

}