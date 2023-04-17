<?php
/**
 * Action Object
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Object;

final class ActionObject extends AbstractObject{

	public string $method;
	public string $url;
	public bool $authorized;

	final public static function build(string $method,string $url,bool $authorized):ActionObject{
		// @todo fare i checks
		return new ActionObject(['method'=>strtoupper($method),'url'=>$url,'authorized'=>$authorized]);
	}

}
