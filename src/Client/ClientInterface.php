<?php
/**
 * Client Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Client;

interface ClientInterface{

	public function getToken():?string;
	public function setToken(string $token):void;
	public function resetToken():void;

	public function get(string $url):mixed;
	public function post(string $url,mixed $body=null):mixed;
	public function put(string $url,mixed $body=null):mixed;
	public function patch(string $url,mixed $body=null):mixed;
	public function delete(string $url):mixed;

}
