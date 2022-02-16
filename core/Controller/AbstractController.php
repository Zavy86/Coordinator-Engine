<?php
/**
 * Abstract Controller
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Controller;

use Coordinator\Engine\Engine;
use Coordinator\Engine\Request\RequestInterface;
use Coordinator\Engine\Response\ResponseInterface;

abstract class AbstractController implements ControllerInterface{

	public function __construct(
	 protected RequestInterface $Request,
	 protected ResponseInterface $Response
	){}

	protected function checkSessionValidity():bool{
		$Session=Engine::getSession();
		//var_dump($Session);
		return $Session->isValid();
	}

	protected function checkAuthorization(string $authorization):bool{  // @todo fare classe specifica
		// @todo implementare
		return false;
	}

	public function debug():array{
		return array(
			'class'=>$this::class
			//'Request'=>$this->Request->debug(),
			//'Response'=>$this->Request->debug()
		);
	}

}