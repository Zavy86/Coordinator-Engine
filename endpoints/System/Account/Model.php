<?php
/**
 * System Account Model
 *
 * @package Coordinator\Engine\System
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Endpoints\System\Account;

use Coordinator\Engine\Model\AbstractModel;

final class Model extends AbstractModel{

	static public string $_service="engine-database";
	static public string $_dataset="system_accounts";

	public static function loadFromAccount(string $account):ModelInterface{
		return static::loadFrom("account",$account);
	}

	protected mixed $uid=null;
	protected string $account;
	protected bool $active;
	protected bool $administrator;
	protected string $handler;
	//protected ?string $twoFactorHash;
	//protected string $name;

	public function isActive():bool{return $this->active;}
	public function isAdministrator():bool{return $this->administrator;}

	public function getAccount():string{return $this->account;}
	public function getHandler():string{return $this->handler;}

}