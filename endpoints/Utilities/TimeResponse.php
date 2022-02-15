<?php
/**
 * Utilities Timestamp Response
 *
 * @package Coordinator\Engine\Endpoints\Utilities
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoints\Utilities;

use Coordinator\Engine\Object\AbstractObject;

class TimeResponse extends AbstractObject{

	public int $timestamp;
	public string $datetime;
	public string $timezone;

	protected function initialization(){
		$this->setProperties([
			'timestamp'=>time(),
			'datetime'=>date('Y-m-d H:i:s'),
			'timezone'=>date_default_timezone_get()
		]);
	}

}