<?php
/**
 * Response Interface
 *
 * @package Coordinator\Engine\Response
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Response;

use Coordinator\Engine\Error\ErrorInterface;
use Coordinator\Engine\Object\ObjectInterface;

interface ResponseInterface{

	/**
	 * Renderize response
	 *
	 * @return string
	 */
	public function render():string;

	/**
	 * Get Response Code
	 *
	 * @return int   @todo replace with ENUM
	 */
	public function getCode():int;

	/**
	 * Get Errors
	 *
	 * @return ErrorInterface[]
	 */
	public function getErrors():array;

	/**
	 * Get Output
	 *
	 * @return ?ObjectInterface
	 */
	public function getObject():?ObjectInterface;

	/**
	 * Set Response Code
	 *
	 * @param int $code      @todo replace with ENUM
	 */
	public function setCode(int $code);

	/**
	 * Add Error
	 *
	 * @param ErrorInterface $Error
	 */
	public function addError(ErrorInterface $Error);

	/**
	 * Set Output
	 *
	 * @param ObjectInterface $Datas
	 */
	public function setObject(ObjectInterface $Datas);

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}