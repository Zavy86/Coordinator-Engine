<?php
/**
 * JSON File Storage
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Storage;

use Coordinator\Engine\Model\ModelInterface;

final class JsonFileStorage extends AbstractStorage{

	protected string $path;
	protected array $cache=[];

	public function __construct(string $path){

		if(!str_ends_with($path,"/")){$path.="/";}
		$this->path=$path;

		$this->checkAndMakePath($path);    // storage path

	}

	private function checkAndMakePath(string $path){
		// @todo spostare in n funzioni private ?
		if(!is_dir($path)){
			@mkdir($path,0755,true);   // @todo verificare permessi adeguati
		}
		if(!is_dir($path)){
			throw StorageException::pathUnavailable($path);
		}
		if(0){  // check if not writable
			throw StorageException::pathNotWritable($path);
		}
	}

	private function getFullPathAndMakeIfNotExists(ModelInterface $Model):string{   // @todo migliorarabile (srp)
		$fullPath=$this->path.$Model::$_dataset.".json";
		if(!$this->checkIfJsonFileExists($fullPath)){
			file_put_contents($fullPath,json_encode(array(),JSON_PRETTY_PRINT));
		}
		return $fullPath;
	}

	private function checkIfJsonFileExists(string $filePath):bool{
		if(!file_exists($filePath)){return false;}
		if(!is_file($filePath)){return false;}
		if(!str_ends_with($filePath,".json")){return false;}
		return true;
	}


	private function getModelFromCache(ModelInterface $Model):array{
		$fullPath=$this->getFullPathAndMakeIfNotExists($Model);
		if(!isset($this->cache[$fullPath])){$this->cacheModelsJsonFile($Model);}
		if(!isset($this->cache[$fullPath])){throw StorageException::cacheError();}
		return $this->cache[$fullPath];
	}

	private function cacheModelsJsonFile(ModelInterface $Model){
		$fullPath=$this->getFullPathAndMakeIfNotExists($Model);
		$bytes=file_get_contents($fullPath);
		$objects=json_decode($bytes,true);
		if(!is_array($objects)){throw StorageException::invalidFormat();}
		// overwrite cache
		$this->cache[$fullPath]=$objects;
	}

	private function storeModelsJson(ModelInterface $Model,$objects){
		$fullPath=$this->getFullPathAndMakeIfNotExists($Model);
		if(!is_array($objects)){throw StorageException::invalidFormat();}
		// overwrite cache
		$this->cache[$fullPath]=$objects;
		// store file
		$bytes=file_put_contents($fullPath,json_encode($objects,JSON_PRETTY_PRINT));  // @best valutare opzione per risparmiare spazio senza pretty print ?
		if(!$bytes){throw StorageException::savingError();}
	}



	public function browse(ModelInterface $Model,?FiltersInterface $filters=null):array{
		$return=array();
		$objects=$this->getModelFromCache($Model);
		if(!is_array($objects)){throw StorageException::invalidFormat();}
		//foreach($objects as $uid=>$object){ @todo chiamare get e usare $uid come chiave (in caso modificare funzione sotto con array_key_exists) [o valutare come fare per i filtri]?
		foreach(array_keys($objects) as $uid){
			$return[]=$uid;
		}
		return $return;
	}


	public function exists(ModelInterface $Model,mixed $uid):bool{
		$objects=$this->browse($Model);
		return in_array($uid,$objects);
	}


	public function get(ModelInterface $Model,mixed $uid):ModelInterface{
		if(!$this->exists($Model,$uid)){throw StorageException::uidNotAvailable($uid);}
		$objects=$this->getModelFromCache($Model);
		foreach($objects[$uid] as $property=>$value){  // @todo chiamare setProperties($properties) e contare return
			$Model->setProperty($property,$value);
		}
		return $Model;
	}


	public function save(ModelInterface $Model):bool{      // @todo @best vedi browse per cache
		$uid=$Model->getUid();
		if(!strlen($uid)){throw StorageException::cannotSaveWithoutUID();}
		$content=$Model->getProperties();
		// get objects
		$objects=$this->getModelFromCache($Model);
		// add or replace by uid
		$objects[$uid]=$content;
		// overwrite file (and overwrite cache @todo migliorabile srp? )
		$this->storeModelsJson($Model,$objects);
		return true; // @todo valutare return or throw
	}


	public function remove(ModelInterface $Model):bool{
		$uid=$Model->getUid();
		if(!strlen($uid)){throw StorageException::cannotRemoveWithoutUID();}
		// get objects
		$objects=$this->getModelFromCache($Model);
		// remove by uid
		unset($objects[$uid]);
		// overwrite file (and overwrite cache @todo migliorabile srp? )
		$this->storeModelsJson($Model,$objects);
		return true;  // @todo valutare return or throw
	}

}