<?php
/**
 * Sign Trait Model
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Model;

use Coordinator\Engine\Engine;

trait SignTraitModel{

	public function getCreateSign():array|null{return $this->getSign('create');}
	public function getUpdateSign():array|null{return $this->getSign('update');}
	public function getCompleteSign():array|null{return $this->getSign('complete');}

	/**
	 * @param string $suffix
	 * @return array return signature [account,timestamp]
	 */
	public function getSign(string $suffix):array|null{
		$account=$this->getProperty($suffix.'Account');
		$timestamp=$this->getProperty($suffix.'Timestamp');
		if($account==null && $timestamp==null){return null;}
		return ['account'=>$account,'timestamp'=>$timestamp];
	}

	public function setCreateSign():void{$this->setSign('create','',0);}
	public function setUpdateSign():void{$this->setSign('update','',0);}
	public function setCompleteSign():void{$this->setSign('complete','',0);}

	public function setSign(string $suffix,string $account='',int $timestamp=0):void{
		if(!strlen($suffix)){throw ModelException::valueNotAcceptable(self::class,'sign suffix is mandatory');}
		if(!strlen($account)){$account=Engine::getSession()->getAccount();}
		if($timestamp==0){$timestamp=time();}
		$this->setAccount($suffix.'Account',$account);
		$this->setTimestamp($suffix.'Timestamp',$timestamp);
	}

	private function setAccount(string $field,string $value):void{
		if(!strlen($value)){throw ModelException::valueNotAcceptable(self::class,$field);};
		// @todo check if account exists?
		$this->{$field}=$value;
	}

	private function setTimestamp(string $field,int $value):void{
		if($value<0){throw ModelException::valueNotAcceptable(self::class,$field);};
		$this->{$field}=$value;
	}

}
