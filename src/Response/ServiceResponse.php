<?php
/**
 * Service Response
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Response;

use Coordinator\Engine\Object\AbstractObject;

final class ServiceResponse extends AbstractObject{

	public string $version;
	public string $title;
	public string $owner;
	public string $host;
	public string $engine;

}
