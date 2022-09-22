<?php
/**
 * Application Configuration
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Configuration;

use Coordinator\Engine\Handler\ConfigurationException;

final class ApplicationConfiguration extends AbstractConfiguration{

	protected bool $debug;
	protected string $namespace;
	protected string $title;
	protected string $owner;
	protected string $secret;

}
