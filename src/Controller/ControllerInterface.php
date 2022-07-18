<?php
/**
 * Controller Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Controller;

use Coordinator\Engine\Request\RequestInterface;
use Coordinator\Engine\Response\ResponseInterface;

interface ControllerInterface{

	/**
	 * Controller constructor
	 *
	 * @param RequestInterface $Request
	 * @param ResponseInterface $Response
	 */
	public function __construct(
		RequestInterface $Request,
		ResponseInterface $Response
	);

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}