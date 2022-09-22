<?php
/**
 * SMTP Configuration
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Configuration;

final class SmtpConfiguration extends AbstractConfiguration{

	protected string $sender_mail;
	protected string $sender_name;
	protected string $host;
	protected string $port;
	protected ?string $security;
	protected ?string $username;
	protected ?string $password;

}