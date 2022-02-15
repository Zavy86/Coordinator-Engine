<?php
/**
 * Endpoint Interface
 *
 * @package Coordinator\Engine\Endpoint
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