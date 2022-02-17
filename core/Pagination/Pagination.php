<?php
/**
 * Pagination
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Pagination;

use Coordinator\Engine\Engine;

final class Pagination implements PaginationInterface{

	protected int $limit;
	protected int $offset;

	public static function fromRequestQuery(int $limitDefault=20,int $limitMax=100):Pagination{
		$query=Engine::getRequest()->getQuery();
		$limit=(int)($query['limit']??0);
		$offset=(int)($query['offset']??0);
		if(!is_int($limit)||$limit<1){$limit=$limitDefault;}
		if($limit>$limitMax){$limit=$limitMax;}
		if(!is_int($offset)){$offset=0;}
		return new Pagination($limit,$offset);
	}

	public function __construct(int $limit=1,int $offset=0){
		$this->setLimit($limit);
		$this->setOffset($offset);
	}

	public function setLimit(int $limit):void{
		if($limit<1){$limit=1;}
		$this->limit=$limit;
	}

	public function setOffset(int $offset):void{
		if($offset<0){$offset=0;}
		$this->offset=$offset;
	}

	public function returnTextual():string{
		return PHP_EOL.'PAGINATION STRING: LIMIT '.$this->limit.' OFFSET '.$this->offset;
	}

	public function getLimit():int{return $this->limit;}
	public function getOffset():int{return $this->offset;}

	public function debug():array{
		return array(
			'class'=>$this::class,
			'limit'=>$this->limit,
			'offset'=>$this->offset
		);
	}

}
