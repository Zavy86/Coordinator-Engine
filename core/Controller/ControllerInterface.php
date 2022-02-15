<?php
/**
 * Controller Interface
 *
 * @package Coordinator\Engine\Controller
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Controller;

use Coordinator\Engine\Request\RequestInterface;
use Coordinator\Engine\Response\ResponseInterface;

interface ControllerInterface{

	 public function __construct(
		RequestInterface $Request,
		ResponseInterface $Response
	 );

}