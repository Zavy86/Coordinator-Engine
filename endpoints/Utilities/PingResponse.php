<?php
/**
 * Utilities Ping Response
 *
 * @package Coordinator\Engine\Endpoints\Utilities
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoints\Utilities;

use Coordinator\Engine\Object\AbstractObject;

class PingResponse extends AbstractObject{

	public string $response='pong';

}