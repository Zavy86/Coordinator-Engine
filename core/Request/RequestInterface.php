<?php
/**
 * Request Interface
 *
 * @package Coordinator\Engine\Request
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Request;

use Coordinator\Engine\Object\ObjectInterface;

interface RequestInterface{

	public function getAddress():string;

	public function getMethod():string;

	public function getUri():string;

	public function getQuery():array;

	public function getBody():array;

	public function getObject(string $class):ObjectInterface;

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}