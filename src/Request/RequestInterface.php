<?php
/**
 * Request Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Request;

use Coordinator\Engine\Filter\FilterInterface;
use Coordinator\Engine\Object\ObjectInterface;
use Coordinator\Engine\Pagination\PaginationInterface;
use Coordinator\Engine\Sorting\SortingInterface;

interface RequestInterface{

	/**
	 * Get Client Address
	 *
	 * @return string
	 */
	public function getAddress():string;

	/**
	 * Get Method
	 *
	 * @return string
	 */
	public function getMethod():string;

	/**
	 * Get URI
	 *
	 * @return string
	 */
	public function getUri():string;

	/**
	 * Get Query
	 *
	 * @return array
	 */
	public function getQuery():array;

	/**
	 * Get Body
	 *
	 * @return array
	 */
	public function getBody():array;

	/**
	 * Has Body
	 *
	 * @return boolean
	 */
	public function hasBody():bool;

	/**
	 * Get Object
	 *
	 * @param string $class
	 * @return ObjectInterface
	 */
	public function getObject(string $class):ObjectInterface;

	/**
	 * Get Search
	 *
	 * @return ?string
	 */
	public function getSearch():?string;

	/**
	 * Get Filter
	 *
	 * @return ?FilterInterface
	 */
	public function getFilter():?FilterInterface;

	/**
	 * Get Sorting
	 *
	 * @return ?SortingInterface
	 */
	public function getSorting(?SortingInterface $defaultSorting=null):?SortingInterface;

	/**
	 * Get Pagination
	 *
	 * @param int $limitDefault
	 * @param int $limitMax
	 * @return ?PaginationInterface
	 */
	public function getPagination(int $limitDefault=20,int $limitMax=100):?PaginationInterface;

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}
