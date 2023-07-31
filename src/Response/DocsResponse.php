<?php
/**
 * Docs Response
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Response;

use Coordinator\Engine\Object\AbstractObject;

final class DocsResponse extends AbstractObject{
	public string $title;
	public string $version;
	/** @var DocsEndpoint[] $enpoints */
	public array $endpoints = [];
}

final class DocsEndpoint{
	public string $method;
	public string $command;
	public ?string $description = null;
	public ?DocsObject $request = null;
	public ?DocsObject $response = null;
}

final class DocsObject{
	public string $name;
	public array $properties = [];
}

final class DocsObjectProperty{
	public string $name;
	public string $typology;
	public string $default;
	public ?string $information;
	public ?DocsObject $class = null;
}