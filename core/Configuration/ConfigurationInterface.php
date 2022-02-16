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
	 * @param string $configuration Name configuration file or configuration in jjson format
	 */
	public function __construct(string $configuration);

	/**
	 * Get property value
	 *
	 * @param string $property Property name
	 * @return mixed
	 */
	public function get(string $property):mixed;

}