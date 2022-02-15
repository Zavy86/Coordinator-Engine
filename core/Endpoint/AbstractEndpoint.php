<?php
/**
 * Abstract Endpoint
 *
 * @package Coordinator\Engine\Endpoint
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

}