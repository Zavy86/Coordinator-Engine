<?php
/**
 * Browse Object
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Object;

final class BrowseObject extends AbstractObject{

	public int $count=0;
	public int $limit=0;
	public int $offset=0;
	public array $uids=[];

}
