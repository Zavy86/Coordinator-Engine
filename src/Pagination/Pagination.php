<?php
/**
 * Pagination
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Pagination;

final class Pagination implements PaginationInterface{

	protected int $limit;
	protected int $offset;

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
