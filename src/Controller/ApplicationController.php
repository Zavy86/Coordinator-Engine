<?php
/**
 * Application Controller
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Controller;

use Coordinator\Engine\Engine;
use Coordinator\Engine\Response\ResponseCode;
use Coordinator\Engine\Response\ServiceResponse;
use Coordinator\Engine\Response\PingResponse;
use Coordinator\Engine\Response\TimeResponse;

final class ApplicationController extends AbstractController{

	/**
	 *
	 */
	public function service(){
		$ServiceResponse=new ServiceResponse();
		$ServiceResponse->version=Engine::$VERSION;
		$ServiceResponse->title=Engine::$TITLE;
		$ServiceResponse->owner=Engine::$OWNER;
		$ServiceResponse->engine=Engine::$ENGINE;
		$this->Response->setCode(ResponseCode::OK_200);
		$this->Response->setObject($ServiceResponse);
	}

	/**
	 * 
	 */
	public function ping(){
		$PingResponse=new PingResponse();
		$PingResponse->response='pong';
		$this->Response->setCode(ResponseCode::OK_200);
		$this->Response->setObject($PingResponse);
	}

	/**
	 * 
	 */
	public function time(){
		$TimeResponse=new TimeResponse();
		$TimeResponse->timestamp=time();
		$TimeResponse->datetime=date('Y-m-d H:i:s');
		$TimeResponse->timezone=date_default_timezone_get();
		$this->Response->setCode(ResponseCode::OK_200);
		$this->Response->setObject($TimeResponse);
	}

}
