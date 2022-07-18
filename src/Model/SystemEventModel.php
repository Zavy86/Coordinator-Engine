<?php
/**
 * System Event Model
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Model;

final class SystemEventModel extends AbstractModel{

	static public string $_service="Coordinator\Engine_database";
	static public string $_dataset="system__events";

	protected mixed $uid=null;
	protected int $timestamp;
	protected string $resource;
	protected string $code;
	protected string $data_json;

	public function getData():array{
		return json_decode($this->data_json,true);
	}

}