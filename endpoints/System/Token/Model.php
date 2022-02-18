<?php
/**
 * System Token Model
 *
 * @package Coordinator\Engine\System
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoints\System\Token;

use Coordinator\Engine\Model\AbstractModel;

final class Model extends AbstractModel{

	static protected int $_uid_length=32;
	static public string $_service="engine-database";
	static public string $_dataset="system_tokens";

	protected mixed $uid=null;
	protected string $label;
	protected string $secret;

	public function getLabel():string{return $this->label;}
	public function getSecret():string{return $this->secret;}

	public function __construct(){
		$this->secret=md5(time());
	}

}