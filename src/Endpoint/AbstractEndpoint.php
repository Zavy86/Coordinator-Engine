<?php
/**
 * Abstract Endpoint
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoint;

use Coordinator\Engine\Router\RouterInterface;

abstract class AbstractEndpoint implements EndpointInterface{

	public function debug():array{
		return array(
			'class'=>$this::class
		);
	}


	/** @var string $uid Endpoint Unique Identifier /Endpoint */
	static protected string $uid;

	static protected string $name;
	static protected string $version;


	/*final public function __construct(string $name){    // fare static?
		$this->checkUid();
		$this->checkFiles();
		$this->loadVersion();
	}*/

	private function checkUid(){
		if(!isset(stati::$uid)){
			throw EndpointException::uidNotDefined(static::class);
		}
	}

	private function checkFiles(){
		// @todo
	}

	private function loadVersion(){
		// @todo
	}

}
