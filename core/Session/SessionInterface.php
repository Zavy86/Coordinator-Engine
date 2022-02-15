<?php
/**
 * Session Interface
 *
 * @package Coordinator\Engine\Session
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Session;

interface SessionInterface{

	public function isValid():bool;

	public function authenticate(string $username,string $password,string $client,string $secret,int $duration):bool;

}