<?php
/**
 * Services Exception
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Services;

final class ServicesException extends \Exception{

	public static function uidDuplicated():static{
		return new static("Service UID already exists.");
	}

	public static function uidNotFound():static{
		return new static("Service UID not found.");
	}

}
