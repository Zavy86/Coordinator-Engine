<?php
/**
 * Error Test
 *
 * @package Coordinator\Engine\Test
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Test\unit;

use Coordinator\Engine\Error\Error;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase{

	public function testGenericError():void{
		$Error=new Error('genericError','generic error description','generic error information');
		$this->assertSame('genericError',$Error->getCode());
		$this->assertSame('generic error description',$Error->getDescription());
		$this->assertSame('generic error information',$Error->getInformation());
	}

}
