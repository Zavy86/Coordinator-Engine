<?php
/**
 * System Password Model
 *
 * @package Coordinator\Engine\System
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoints\System\Model;

use Coordinator\Engine\Model\AbstractModel;

final class SystemPasswordModel extends AbstractModel{

	static public string $_service="engine-database";
	static public string $_dataset="system_password";

	public static function loadFromUidAccount(string $uidAccount):ModelInterface{
		return static::loadFrom("uidAccount",$uidAccount);
	}

	protected mixed $uid=null;
	protected string $uidAccount;
	protected string $passwordHash;
	protected bool $expired;

	public function isExpired():bool{return $this->expired;}

	public function checkPassword(string $password):bool{
		return (hash($password)===$this->passwordHash);
	}

	public function getAccount():string{return $this->account;}
	public function getHandler():string{return $this->handler;}

}