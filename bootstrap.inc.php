<?php
/**
 * Bootstrap
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */
error_reporting(E_ALL);
ini_set('display_errors',(isset($_GET['debug']) && $_GET['debug']==1));
define('DIR',str_replace(['/','\\'],DIRECTORY_SEPARATOR,__DIR__.'/'));
if(version_compare(PHP_VERSION,'8.1.0')<0){die('Required at least PHP version 8.1.0, current version: '.PHP_VERSION);}

/** @todo migliorare autoloader specificando magari le classi in un file di configurazione? */

// autoload configuration
spl_autoload_register(function($class){
	//var_dump('Loading class '.$class);
	$autoloadable_classes=array(
		'Coordinator\Engine\Endpoints\\'=>'/endpoints/',
		'Coordinator\Engine\\'=>'/core/'
	);
	foreach($autoloadable_classes as $prefix=>$path){
		if(strncmp($prefix,$class,strlen($prefix))!==0){continue;}
		$base_dir=__DIR__.$path;
		$relative_class=substr($class,strlen($prefix));
		$file=str_replace(['/','\\'],DIRECTORY_SEPARATOR,$base_dir.$relative_class.'.php');
		if(!file_exists($file)){
			http_response_code(500);
			throw new Exception('Error auto-loading class '.$class.'. File '.$file.' was not found');
		}
		require_once($file);
		break;
	}
});
