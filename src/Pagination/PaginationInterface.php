<?php
/**
 * Pagination Interface
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Pagination;

interface PaginationInterface{

	public function __construct(int $limit=0,int $offset=0);

	public function setLimit(int $limit):void;
	public function setOffset(int $offset):void;

	public function getLimit():int;
	public function getOffset():int;

	public function debug():array;

}
