<?php
/**
 * Parts Trait Controller
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Controller;

trait PartsTraitController{

	/**
	 * @var string[] requested parts
	 */
	private array $_parts=[];

	/**
	 * load parts from request
	 *
	 * @return void
	 */
	private function _loadParts():void{
		$this->_parts=[];
		$query=$this->Request->getQuery();
		if(array_key_exists('parts',$query) && strlen($query['parts'])){
			foreach(explode(',',$query['parts']) as $part){
				if(!strlen($part)){continue;}
				$this->_parts[]=strtolower($part);
			}
		}
	}

	/**
	 * get requested parts
	 *
	 * @return string[]
	 */
	public function getParts():array{
		if(empty($this->_parts)){$this->_loadParts();}
		return $this->_parts;
	}

	/**
	 * check if any parts has requested
	 *
	 * @return bool
	 */
	public function hasParts():bool{
		return (count($this->getParts())>0);
	}

	/**
	 * check if a specific part has requested
	 *
	 * @param string $part
	 * @return bool
	 */
	public function hasPart(string $part):bool{
		return in_array(strtolower($part),$this->getParts());
	}

}
