<?php
/**
 * Session Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Session;

interface SessionInterface{

	/**
	 * Check if session is valid
	 *
	 * @return bool
	 */
	public function isValid():bool;

	/**
	 * Try to Authenticate
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $client
	 * @param string $secret
	 * @param int $duration
	 * @return bool
	 */
	public function authenticate(string $username,string $password,string $client,string $secret,int $duration):bool;

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}