<?php
/**
 * Error Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Error;

interface ErrorInterface{

	/**
	 * Constructor
	 * @param string $code Error code
	 * @param ?string $description Error description
	 * @param ?string $information Error information
	 */
	public function __construct(string $code,?string $description=null,?string $information=null);

	public function output():array;

	public function getCode():string;
	public function getDescription():?string;
	public function getInformation():?string;

	public function setCode(string $code):bool;
	public function setDescription(?string $description=null):bool;
	public function setInformation(?string $information=null):bool;

}