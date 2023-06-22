<?php
/**
 * JSON Directory Storage
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Storage;

use Coordinator\Engine\Filter\FilterInterface;
use Coordinator\Engine\Model\ModelInterface;
use Coordinator\Engine\Pagination\PaginationInterface;
use Coordinator\Engine\Sorting\SortingInterface;

final class JsonDirectoryStorage extends AbstractStorage{

	protected string $path;
	//static protected string $dataset;

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
		$fullPath=$this->path.$Model::$_dataset;
		if(!str_ends_with($fullPath,"/")){$fullPath.="/";}
		$this->checkAndMakePath($fullPath);                        //   dataset path      @todo try?
		return $fullPath;
	}

	private function checkIfJsonFileExistsAndNotEmpty(string $filePath):bool{
		if(!file_exists($filePath)){return false;}
		if(!is_file($filePath)){return false;}
		if(!filesize($filePath)){unlink($filePath);return false;}
		if(!str_ends_with($filePath,".json")){return false;}
		return true;
	}

	public function count(ModelInterface $Model,?FilterInterface $Filter=null):int{
		return 0; // @todo
	}

	public function browse(ModelInterface $Model,?FilterInterface $Filter=null,?SortingInterface $Sorting=null,?PaginationInterface $Pagination=null):array{
		$path=$this->getFullPathAndMakeIfNotExists($Model);
		$files=array();
		foreach(scandir($path) as $file){   // @todo ->getDataset()?
			if(!$this->checkIfJsonFileExistsAndNotEmpty($path.$file)){continue;}
			$files[]=substr($file,0,-5);
		}
		return $files;
	}


	public function exists(ModelInterface $Model,mixed $uid):bool{
		$path=$this->getFullPathAndMakeIfNotExists($Model);
		if(!$this->checkIfJsonFileExistsAndNotEmpty($path.$uid.".json")){return false;}
		return true;
	}


	public function load(ModelInterface $Model,mixed $uid):ModelInterface{
		if(!$this->exists($Model,$uid)){throw StorageException::uidNotAvailable($uid);}
		$path=$this->getFullPathAndMakeIfNotExists($Model);
		$bytes=file_get_contents($path.$uid.".json");
		$properties=json_decode($bytes,true);
		foreach($properties as $property=>$value){  // @todo chiamare setProperties($properties) e contare return
			$Model->setProperty($property,$value);
		}
		return $Model;
	}

	public function loadFromKey(ModelInterface $Model,string $key,mixed $value,mixed &$uid):ModelInterface{
		// @todo
	}

	public function loadFromKeys(ModelInterface $Model,array $keys,mixed &$uid):ModelInterface{
		// @todo
	}

	public function save(ModelInterface $Model):bool{
		$uid=$Model->getUid();
		if(!strlen($uid)){throw StorageException::cannotSaveWithoutUID();}
		$path=$this->getFullPathAndMakeIfNotExists($Model);
		$content=$Model->getProperties();
		$bytes=file_put_contents($path.$uid.".json",json_encode($content,JSON_PRETTY_PRINT));  // @best valutare opzione per risparmiare spazio senza pretty print ?
		if($bytes>0){return true;}else{return false;}  // trown  instead of bool?
	}


	public function remove(ModelInterface $Model):bool{
		$uid=$Model->getUid();
		if(!strlen($uid)){throw StorageException::cannotRemoveWithoutUID();}
		if(!$this->exists($Model,$uid)){throw StorageException::uidNotAvailable($uid);}
		$path=$this->getFullPathAndMakeIfNotExists($Model);
		return unlink($path.$uid.".json");  // trown  instead of bool?
	}

}
