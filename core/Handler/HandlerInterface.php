<?php
/**
 * Handler Interface
 *
 * @package Coordinator\Engine\Handler
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Handler;

use Coordinator\Engine\Request\RequestInterface;
use Coordinator\Engine\Response\ResponseInterface;

interface HandlerInterface{

	public function __construct(
	 RequestInterface $Request,
	 ResponseInterface $Response
	);

	public function handle();

}