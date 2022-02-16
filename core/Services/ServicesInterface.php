<?php
/**
 * Services Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Services;

interface ServicesInterface{

	public static function exists(string $uid):bool;

	/**
	 * Add Service
	 *
	 * @param string $uid Service UID
	 * @param mixed $Service Service instance
	 */
	public static function add(string $uid,mixed $Service):void;

	/**
	 * Get Service, if not exist throw a ServicesException
	 *
	 * @param string $uid Service UID
	 * @throws ServicesException
	 * @return mixed
	 */
	public static function getRequired(string $uid):mixed;

	/**
	 * Get Service, if not exist return null
	 *
	 * @param string $uid Service UID
	 * @return mixed
	 */
	public static function getOptional(string $uid):mixed;

	/**
	 * Debug
	 *
	 * @return array
	 */
	public static function debug():array;

}