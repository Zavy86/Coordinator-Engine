<?php
/**
 * Configuration Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Configuration;

interface ConfigurationInterface{

	/**
	 * Load configuration
	 *
	 * @param string|array $configuration Name configuration file or array of configuration parameters
	 */
	public function __construct(string|array $configuration);

	/**
	 * Get property value
	 *
	 * @param string $property Property name
	 * @return mixed
	 */
	public function get(string $property):mixed;

}