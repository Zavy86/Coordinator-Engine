<?php
/**
 * System Token
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Model;

final class SystemTokenModel extends AbstractModel{

	static protected int $_uid_length=32;
	static public string $_service="Coordinator\Engine_database";
	static public string $_dataset="system__tokens";

	protected mixed $uid=null;
	protected string $secret;
	protected string $label;
	protected bool $active;

}