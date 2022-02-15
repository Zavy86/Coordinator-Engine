<?php
/**
 * Bootstrap
 *
 * @package Coordinator\Engine     @todo valutare se spostare o togliere engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

// check for php version
if(version_compare(PHP_VERSION,'8.1.0')<0){die('Required at least PHP version 8.0.0, current version: '.PHP_VERSION);}

// check for debug                 @todo valutare se tenere
global $debug;
if(isset($_GET['debug']) && $_GET['debug']==1){$debug=true;}else{$debug=false;}

// errors configuration
error_reporting(E_ALL);
ini_set('display_errors',$debug);

/** @todo migliorare autoloader */

// modules
spl_autoload_register(function ($class){
	//echo '<br>class '.$class.' auto-loading<br>';
	// project-specific namespace prefix
	$prefix='Coordinator\\Engine\\Modules\\';
	// base directory for the namespace prefix
	$base_dir=__DIR__.'/modules/';
	// does the class use the namespace prefix?
	$len = strlen($prefix);
	if(strncmp($prefix, $class, $len)!==0){
		// no, move to the next registered autoloader
		return;
	}
	// get the relative class name
	$relative_class=substr($class,$len);
	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file=$base_dir.str_replace('\\','/',$relative_class).'.php';
	// if the file exists, require it
	if(file_exists($file)){
		require $file;
	}else{
		throw new Exception('Error auto-loading class '.$class.'. File '.$file.' was not found');
		die();
	}
});

// core
spl_autoload_register(function ($class){
	//echo '<br>class '.$class.' auto-loading<br>';
	// project-specific namespace prefix
	$prefix='Coordinator\\Engine\\';
	// base directory for the namespace prefix
	$base_dir=__DIR__.'/core/';
	// does the class use the namespace prefix?
	$len = strlen($prefix);
	if(strncmp($prefix, $class, $len)!==0){
		// no, move to the next registered autoloader
		return;
	}
	// get the relative class name
	$relative_class=substr($class,$len);
	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file=$base_dir.str_replace('\\','/',$relative_class).'.php';
	// if the file exists, require it
	if(file_exists($file)){
		require $file;
	}else{
		throw new Exception('Error auto-loading class '.$class.'. File '.$file.' was not found');
		die();
	}
});
