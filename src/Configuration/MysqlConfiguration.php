<?php
/**
 * Database Configuration
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Configuration;

final class MysqlConfiguration extends AbstractConfiguration{

	protected string $host;
	protected int $port;
	protected string $charset;
	protected string $database;
	protected string $username_read;
	protected string $password_read;
	protected string $username_write;
	protected string $password_write;
	protected string $username_admin;
	protected string $password_admin;

}