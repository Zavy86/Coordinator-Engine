<?php
/**
 * System Session
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Model;

use Coordinator\Engine\Handler\Conditions;
use Coordinator\Engine\Handler\Filter;
use Coordinator\Engine\Handler\isBetween;
use Coordinator\Engine\Handler\isLesserThan;
use Coordinator\Engine\Handler\isLike;
use Coordinator\Engine\Handler\isNotBetween;

final class SystemSessionModel extends AbstractModel{

	static protected int $_uid_length=32;
	static public string $_service="Coordinator\Engine_database";
	static public string $_dataset="system__sessions";

	protected mixed $uid=null;
	protected string $uidAccount;
	protected int $createTimestamp;
	protected int $updateTimestamp;

	public static function destroy_expired(){
		$update_validity=(60*60*3);           // @todo caricare da configurazione
		$create_validity=(60*60*24);
		$Filter=new Filter(
		 new Conditions('OR',
		 	new isLesserThan('updateTimestamp',(time()-$update_validity)),
		 	new isLesserThan('createTimestamp',(time()-$create_validity))
		 )
		);
		foreach(SystemSessionModel::browse($Filter) as $uidSystemSession){
			var_dump($uidSystemSession);
			SystemSessionModel::get($uidSystemSession)->remove();
		}
	}

	public function isExpired():bool{
		$update_validity=(60*60*3);           // @todo caricare da configurazione
		$create_validity=(60*60*24);
		if((time()-$this->getProperty('updateTimestamp'))>$update_validity){return true;}
		if((time()-$this->getProperty('createTimestamp'))>$create_validity){return true;}
		return false;
	}

}
