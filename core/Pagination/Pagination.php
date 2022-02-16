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

	public function __construct(int $limit=0,int $offset=0){
		$this->setLimit($limit);
		$this->setOffset($offset);
	}

	public function setLimit(int $limit){$this->limit=$limit;}
	public function setOffset(int $offset){$this->offset=$offset;}

	public function returnTextual():string{
		return PHP_EOL.'PAGINATION STRING: LIMIT '.$this->limit.' OFFSET '.$this->offset;
	}

	public function getLimit():int{return $this->limit;}
	public function getOffset():int{return $this->offset;}

}
