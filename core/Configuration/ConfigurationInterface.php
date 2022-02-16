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
	 * @param string $configurationFilePath Full path of the configuration file
	 */
	public function __construct(string $configurationFilePath);

	/**
	 * Get property value
	 *
	 * @param string $property Property name
	 * @return mixed
	 */
	public function get(string $property):mixed;

}