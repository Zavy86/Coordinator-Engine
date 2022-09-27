<?php
/**
 * System Log
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Model;

use Coordinator\Engine\Error\ErrorInterface;

final class SystemLogModel extends AbstractModel{

	static protected int $_uid_length=32;
	static public string $_service="Coordinator\Engine_database";
	static public string $_dataset="system__logs";

	protected mixed $uid=null;
	protected int $timestamp;
	protected string $token;
	protected string $module;
	protected string $command;
	protected string $remoteAddress;
	protected string $remoteAccount;
	protected int $response;
	protected ?string $errors_json=null;

	public function getErrors():array{
		$errors=json_decode($this->data_json,true);
		if(!is_array($errors)){$errors=array();}           // @todo fare funzione per deserializzare direttamente in ErroInterface

		// @todo ciclare e creare classi event

		return $errors;
	}

	public function setErrors(array $errors){
		$errors_parsed=array();
		/** @var ErrorInterface $error */
		foreach($errors as $error){
			$errors_parsed[]=array(
			 'code'=>$error->getCode(),
			 'description'=>$error->getDescription(),        // @todo fare funzione per serializzare direttamente in ErroInterface
			 'information'=>$error->getInformation(),
			);
		}
		// @todo checks and throws
		$this->errors_json=json_encode($errors_parsed,JSON_PRETTY_PRINT);   // @todo levare dopo debug per comprimere
	}

}
