<?php
/**
 * Abstract Controller
 *
 * @package Coordinator\Engine\Controller
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Controller;

use Coordinator\Engine\Request\RequestInterface;
use Coordinator\Engine\Response\ResponseInterface;
use Coordinator\Engine\Services\Services;
use Coordinator\Engine\Session\Session;

abstract class AbstractController implements ControllerInterface{

	public function __construct(
	 protected RequestInterface $Request,
	 protected ResponseInterface $Response
	){}

	protected function checkSessionOrRedirectToLogin(){
		/** @var Session $Session */
		$Session=Services::getService("session");

		// @todo check
		if(!$Session->isValid()){
			header('location:/admin/authentication/login');
			$this->Response->setCode(301);
			return;   // valutare come funziona.. se usare return o exit o cosa..
		}
	}

	protected function checkAuthorizationOrRedirect(string $authorization,string $redirect_url='/admin/index'){
		// @todo check
		if(false){  //$authorization
			header('location:'.$redirect_url);
			$this->Response->setCode(301);
			return;   // valutare come funziona.. se usare return o exit o cosa..
		}
	}

}