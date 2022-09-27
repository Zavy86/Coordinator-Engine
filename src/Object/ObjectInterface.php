<?php
/**
 * Model Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Object;

interface ObjectInterface{

	/**
	 * Create model
	 *
	 * @param array $properties Array of properties key=>value
	 */
	public function __construct(array $properties=array());

	/**
	 * Set Model Properties
	 *
	 * @param array $properties Array of properties key=>value
	 */
	public function setProperties(array $properties):void;

	/**
	 * Get Model Properties
	 *
	 * @return array
	 */
	public function getProperties():array;

	/**
	 * Get Model Property
	 *
	 * @param string $property
	 * @return mixed
	 */
	public function get(string $property):mixed;

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}
