<?php
/**
 * Authentication Login Response
 *
 * @package Coordinator\Engine\Authentication
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoints\Authentication;

use Coordinator\Engine\Object\AbstractObject;

final class LoginResponse extends AbstractObject{

	public string $token;
	public int $duration;
	public int $generation;
	public int $expiration;
	/*public string $refreshToken  @todo */

}