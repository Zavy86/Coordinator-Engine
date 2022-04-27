<?php
/**
 * Timestamp
 *
 * @package Coordinator\Engine\Functions
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Functions;

class Timestamp{

	protected int $time;

	public function __construct(?int $time=null){
		$this->time=($time??time());
	}

	public function getTime():int{
		return $this->time;
	}

	public function format(string $format='Y-m-d H:i:s'){
		return date($format,$this->getTime());
	}

}
