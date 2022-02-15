<?php
/**
 * Authentication Login Request
 *
 * @package Coordinator\Engine\Endpoints\Utilities
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoints\Authentication;

use Coordinator\Engine\Object\AbstractObject;

final class LoginRequest extends AbstractObject{

	public string $username;
	public string $password;
	public string $client;
	public string $secret;
	/*public string $remoteAddress;  @todo valutare se serve o se arriva gia giusto da angular */
	public int $duration;

}