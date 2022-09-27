<?php
/**
 * Response Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Response;

use Coordinator\Engine\Error\ErrorInterface;
use Coordinator\Engine\Model\ModelInterface;
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
	 * @return ResponseCode
	 */
	public function getCode():ResponseCode;

	/**
	 * Get Errors
	 *
	 * @return ErrorInterface[]
	 */
	public function getErrors():array;

	/**
	 * Get Output
	 *
	 * @return ObjectInterface|ModelInterface|null
	 */
	public function getObject():ObjectInterface|ModelInterface|null;

	/**
	 * Set Response Code
	 *
	 * @param ResponseCode $code
	 */
	public function setCode(ResponseCode $code);

	/**
	 * Add Error
	 *
	 * @param ErrorInterface $Error
	 */
	public function addError(ErrorInterface $Error);

	/**
	 * Set Output
	 *
	 * @param ObjectInterface|ModelInterface $Datas
	 */
	public function setObject(ObjectInterface|ModelInterface $Object);

	/**
	 * Debug
	 *
	 * @return array
	 */
	public function debug():array;

}
