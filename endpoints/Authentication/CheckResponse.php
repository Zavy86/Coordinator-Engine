<?php
/**
 * Authentication Check Response
 *
 * @package Coordinator\Engine\Endpoints\Utilities
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoints\Authentication;

use Coordinator\Engine\Object\AbstractObject;

class CheckResponse extends AbstractObject{

	public bool $valid;
	public string $address;
	public string $username;
	public string $client;
	public string $duration;
	public string $remaining;
	public string $generation;
	public string $expiration;

}