<?php
/**
 * Request Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Request;

use Coordinator\Engine\Object\ObjectInterface;

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
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}
