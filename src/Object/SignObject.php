<?php
/**
 * Sign Object
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Object;

final class SignObject extends AbstractObject{

	public string $account;
	public int $timestamp;

	public static function build(array|null $properties):SignObject|null{
		if($properties==null || !is_array($properties)){return null;}
		if($properties['account']==null || $properties['timestamp']==null){return null;}
		return new SignObject($properties);
	}

}
