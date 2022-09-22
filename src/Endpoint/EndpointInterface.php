<?php
/**
 * Endpoint Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoint;

interface EndpointInterface{

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}
