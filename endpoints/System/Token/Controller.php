<?php
/**
 * System Token Controller
 *
 * @package Coordinator\Engine\System
 * @author Manuel Zavatta <manuel.zavatta@cogne.com>
 */

namespace Coordinator\Engine\Endpoints\System\Token;

use Coordinator\Engine\Controller\AbstractController;

class Controller extends AbstractController{

	public function browse():void{
		if(!$this->check('system.token.view')){return;}
		$this->_browse(Model::class);
	}

	public function load(){
		if(!$this->check('system.token.view')){return;}
		$this->_load(Model::class);
	}

	public function create(){
		if(!$this->check('system.token.edit')){return;}
		$this->_create(Model::class);
		// @todo verificare di non aver passato il secret.. deve essere generato casualmente
	}

	public function update(){
		if(!$this->check('system.token.edit')){return;}
		$this->_update(Model::class);
	}

	public function remove(){
		if(!$this->check('system.token.edit')){return;}
		$this->_remove(Model::class);
	}

}