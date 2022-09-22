<?php
/**
 * Boolean Object
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Object;

final class BooleanObject extends AbstractObject{

	public bool $response;

	private static function build(bool $response):self{
		return new self(['response'=>$response]);
	}

	public static function true():self{
		return self::build(true);
	}

	public static function false():self{
		return self::build(false);
	}

}
