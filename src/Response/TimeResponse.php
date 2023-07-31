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

	#[Information('UNIX timestamp')]
	public int $timestamp;
	#[Information('YYYY-MM-DD HH:MM:SS')]
	public string $datetime;
	public string $timezone;

}
