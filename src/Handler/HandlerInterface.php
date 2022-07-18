<?php
/**
 * Handler Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Handler;

use Coordinator\Engine\Request\RequestInterface;
use Coordinator\Engine\Response\ResponseInterface;

interface HandlerInterface{

	/**
	 * Handler constructor
	 *
	 * @param RequestInterface $Request
	 * @param ResponseInterface $Response
	 */
	public function __construct(
	 RequestInterface $Request,
	 ResponseInterface $Response
	);

	/**
	 * Handle
	 */
	public function handle():void;

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}