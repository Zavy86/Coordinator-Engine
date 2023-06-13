<?php
/**
 * Event Object
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Object;

final class EventObject extends AbstractObject{

	public string $event;
	public int $timestamp;
	public string $account;
	public mixed $data;

}
