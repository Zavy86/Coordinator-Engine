<?php
/**
 * Coordinator Engine
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine;

use Coordinator\Engine\Configuration\ApplicationConfiguration;

use Coordinator\Engine\Services\Services;
use Coordinator\Engine\Session\Session;

use Coordinator\Engine\Request\Request;
use Coordinator\Engine\Response\Response;
use Coordinator\Engine\Handler\Handler;

use Coordinator\Engine\Event\EventInterface;
use Coordinator\Engine\Logger\LoggerInterface;

final class Engine{

	public static bool $DEBUG;
	public static string $VERSION;
	public static string $NAMESPACE;
	public static string $TITLE;
	public static string $OWNER;
	public static string $DIR;
	public static string $URL;
	public static string $PATH;
	public static string $ENGINE;

	private static ?Engine $SINGLETON=null;

	private static ApplicationConfiguration $Configuration;    // @todo valutare se tenere statiche o meno
	private static ?LoggerInterface $Logger;
	private static Handler $Handler;

	private static Session $Session;

	/**
	 * Engine Single Entry Point    @todo migliorare descrizione
	 */
	public static function run(){
		if(is_null(static::$SINGLETON)){
			static::$SINGLETON=new static();
		}
		static::$Handler->handle();   /* @todo valutare se serve fare altro */
		static::debug();   /* @todo valutare se tenere dopo per via degli headers */
	}

	private function __construct(){
		$this->setParameters();
		$this->loadEngineVersion();
		$this->loadApplication();
		$this->loadConfiguration();
		$this->loadSession();
		$this->loadHandler();
	}

	private function setParameters(){
		static::$DEBUG=(isset($_GET['debug']) && $_GET['debug']==1);
		static::$DIR=str_replace(['/','\\'],DIRECTORY_SEPARATOR,dirname(__DIR__).'/');
		static::$URL=(isset($_SERVER["HTTPS"])?"https":"http")."://".$_SERVER["HTTP_HOST"].'/';
	}

	private function loadEngineVersion(){
		if(!file_exists(static::$DIR."../../../vendor/coordinator/engine/composer.json")){throw new \Exception('engine composer.json file not found.');}
		$bytes=file_get_contents(static::$DIR."../../../vendor/coordinator/engine/composer.json");
		$parameters=json_decode($bytes,true);
		if(!is_array($parameters)){throw new \Exception('engine composer.json file syntax error.');}
		if(!array_key_exists('version',$parameters)){throw new \Exception('engine version is not defined.');}
		static::$ENGINE=$parameters['version'];
	}

	private function loadApplication(){
		if(!file_exists(static::$DIR."../../../composer.json")){throw new \Exception('application composer.json file not found.');}
		$bytes=file_get_contents(static::$DIR."../../../composer.json");
		$parameters=json_decode($bytes,true);
		if(!is_array($parameters)){throw new \Exception('application composer.json file syntax error.');}
		if(!array_key_exists('version',$parameters)){throw new \Exception('application version is not defined.');}
		if(!array_key_exists('title',$parameters)){throw new \Exception('application title is not defined.');}
		if(!array_key_exists('owner',$parameters)){throw new \Exception('application owner is not defined.');}
		if(!array_key_exists('autoload',$parameters)){throw new \Exception('application autoload is not defined.');}
		if(!array_key_exists('psr-4',$parameters['autoload'])){throw new \Exception('application psr-4 autoload is not defined.');}
		static::$VERSION=$parameters['version'];
		static::$TITLE=$parameters['title'];
		static::$OWNER=$parameters['owner'];
		foreach($parameters['autoload']['psr-4'] as $namespace=>$directory){if(str_contains($directory,'src')){static::$NAMESPACE=$namespace;}}
		if(!isset(static::$NAMESPACE)){throw new \Exception('unable to retrieve application namespace.');}
	}

	private function loadConfiguration(){
		$Configuration=new ApplicationConfiguration('application.json');
		//var_dump($Configuration);
		static::$Configuration=$Configuration;
		if(!$Configuration->get('debug')){static::$DEBUG=false;}
	}

	private function loadSession(){
		static::$Session=new Session();
		//var_dump(static::$Session);
	}

	private function loadHandler(){
		$Request=new Request();
		//var_dump($Request);
		$Response=new Response();
		//var_dump($Response);
		$Handler=new Handler($Request,$Response);
		//var_dump($Handler);
		static::$Handler=$Handler;
	}

	public static function checkAdministratorPassword(string $password):bool{
		return ($password===static::$Configuration->get('password'));
	}

	public static function debug(){
		if(static::$DEBUG){
			$debug=array(
				'DEBUG'=>static::$DEBUG,
				'VERSION'=>static::$VERSION,
				'TITLE'=>static::$TITLE,
				'OWNER'=>static::$OWNER,
				'NAMESPACE'=>static::$NAMESPACE,
				'DIR'=>static::$DIR,
				'URL'=>static::$URL,
				'ENGINE'=>static::$ENGINE,
				'Configuration'=>static::$Configuration,
				//'Logger'=>static::$Logger,
				'Handler'=>static::$Handler->debug(),
				'Services'=>Services::debug()
			);
			//print_r($debug);
			var_dump($debug);
		}
	}

	public static function getSession(){
		return static::$Session;
	}

	public static function getHandler(){
		return static::$Handler;
	}

	public static function getRequest(){
		return static::$Handler->getRequest();
	}

	public static function getResponse(){
		return static::$Handler->getResponse();
	}

	/**
	 * Log an event and publish it to listeners
	 * @param EventInterface $Event
	 * @return bool
	 */
	public function log(EventInterface $Event):bool{
		if(is_null(static::$Logger)){return false;}
		return static::$Logger->publish($Event);
	}

}
