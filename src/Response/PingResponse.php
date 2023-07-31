<?php
/**
 * Ping Response
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Response;

use Coordinator\Engine\Object\AbstractObject;

final class PingResponse extends AbstractObject{

	public string $response = 'pong';

}
