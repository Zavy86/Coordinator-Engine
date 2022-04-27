<?php
/**
 * Engine
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

use \Coordinator\Engine\Engine;
use \Coordinator\Engine\Services\Services;
use \Coordinator\Engine\Storage\MysqlStorage;
use \Coordinator\Engine\Configuration\MysqlConfiguration;

require_once('../bootstrap.inc.php');

Services::add('engine-database',new MysqlStorage(new MysqlConfiguration('engine.database.json')));

Engine::run();
