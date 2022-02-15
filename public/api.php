<?php
/**
 * API
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

/*
include('../core/Response/ResponseCode.php');
var_dump(\Coordinator\Engine\Response\ResponseCode::OK_200);
var_dump(\Coordinator\Engine\Response\ResponseCode::OK_200->value);
var_dump(\Coordinator\Engine\Response\ResponseCode::OK_200->name);
*/

require_once("../bootstrap.inc.php");
\Coordinator\Engine\Engine::run();

/*

//
use \Coordinator\Engine\Configuration\ApplicationConfiguration;

// Load Application Configuration
$ApplicationConfiguration=new ApplicationConfiguration(DIR.'configurations/application.json');
//var_dump($ApplicationConfiguration);

// Load Database Configuration
//$DatabaseConfiguration=new \Coordinator\Engine\Configuration\DatabaseConfiguration(DIR.'configurations/database.json');
//var_dump($DatabaseConfiguration);

// Build Logger
//$Logger=new \Coordinator\Engine\Logger\Logger();

// Check for debug
if(DEBUG){
	// Build new Print Listener and add subscribe to logger for all resources and all events
	//$Logger->subscribe(new \Coordinator\Engine\Listener\PrintListener(),"*","*");
}
/**
 * @var \Coordinator\Engine\Services\ServicesInterface $Services
 */

// Add Logger to Services
//$Services->addService('logger',$Logger);

//$Database=new \Coordinator\Engine\Storage\MysqlStorage($DatabaseConfiguration);    // trasformare in DatabaseStorage (con Mysql come parametro ? )
//var_dump($Database);

// Add Database to Services
//$Services->addService('Coordinator\Engine_database',$Database);

// Build Token Session
//$TokenSession=new \Coordinator\Engine\Session\TokenSession();
//var_dump($TokenSession,"TokenSession");

// Add Session to Services
//$Services->addService('session',$TokenSession);

//debug((new \Coordinator\Engine\Request\Request()));

// Build REST Proxy
//$Proxy=new \Coordinator\Engine\Proxy\RestProxy((new \Coordinator\Engine\Request\Request()),(new \Coordinator\Engine\Response\Response()));
//var_dump($Proxy);

// Build Application
//$Application=new \Coordinator\Engine\Engine($ApplicationConfiguration,$Proxy);

//$Application->log(new \Coordinator\Engine\Event\Event($Application::class,"running"));

//$Application->run();

//var_dump($Application,"Application");




