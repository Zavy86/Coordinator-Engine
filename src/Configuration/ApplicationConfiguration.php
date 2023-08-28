<?php
/**
 * Application Configuration
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Configuration;

final class ApplicationConfiguration extends AbstractConfiguration{

	protected bool $debug;
	protected string $path;
	protected string $secret;

}
