<?php
/**
 * Time Response
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Response;

use Coordinator\Engine\Object\AbstractObject;

final class TimeResponse extends AbstractObject{

	public int $timestamp;
	public string $datetime;
	public string $timezone;

}
