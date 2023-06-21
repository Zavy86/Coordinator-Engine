<?php
/**
 * MySQL Storage
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Storage;

use Coordinator\Engine\Filter\Condition;
use Coordinator\Engine\Filter\Conditions;

use Coordinator\Engine\Logger\LoggerInterface;

use Coordinator\Engine\Configuration\MysqlConfiguration;
use Coordinator\Engine\Services\Services;

use Coordinator\Engine\Model\ModelInterface;
use Coordinator\Engine\Filter\FilterInterface;
use Coordinator\Engine\Sorting\SortingInterface;
use Coordinator\Engine\Pagination\PaginationInterface;

use PDO;
use PDOException;

final class MysqlStorage extends AbstractStorage{

	protected PDO $connection_read;
	protected PDO $connection_write;
	//protected PDO $connection_admin;

	protected int $counter_queries_executed=0;
	protected int $counter_queries_from_cached=0;

	/** @var array $cache with query key and result value */
	protected array $cache=[];

	/**
	 * Services
	 */
	protected ?LoggerInterface $Logger;

	public function __construct(
		protected MysqlConfiguration $Configuration
	){
		// get logger
		$this->Logger=Services::getOptional("logger");
		// make dsn
		$dsn='mysql'.
			':host='.$Configuration->get('host').
			';port='.$Configuration->get('port').
			';dbname='.$Configuration->get('database').
			';charset='.$Configuration->get('charset');
		// try to connect for read
		try{
			$this->connection_read=new PDO($dsn,$Configuration->get('username_read'),$Configuration->get('password_read'));     // @todo migliorare
			$this->connection_read->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,"SET NAMES ".$Configuration->get('charset'));
			$this->connection_read->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
			$this->connection_read->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);           // @todo verificare attributi
			$this->log("connection_read_success","Connected to ".$Configuration->get('database')." database on server ".$Configuration->get('host')." in read mode");
		}catch(PDOException $e){
			// connection failed
			$this->log("connection_read_error","Error connecting to ".$Configuration->get('database')." database on server ".$Configuration->get('host')." in read mode");
			throw StorageException::genericException("Error connecting to ".$Configuration->get('database')." database on server ".$Configuration->get('host')." in read mode");
		}
		// try to connect for write
		try{
			$this->connection_write=new PDO($dsn,$Configuration->get('username_write'),$Configuration->get('password_write'));
			$this->connection_write->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,"SET NAMES ".$Configuration->get('charset'));
			$this->connection_write->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
			$this->connection_write->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);           // @todo verificare attributi
			$this->log("connection_write_success","Connected to ".$Configuration->get('database')." database on server ".$Configuration->get('host')." in write mode");
		}catch(PDOException $e){
			// connection failed
			$this->log("connection_write_error","Error connecting to ".$Configuration->get('database')." database on server ".$Configuration->get('host')." in write mode");
			throw StorageException::genericException("Error connecting to ".$Configuration->get('database')." database on server ".$Configuration->get('host')." in write mode");
		}
	}

	public function __destruct(){
		$this->log("queries_counter","Queries executed: ".$this->counter_queries_executed.", Queries retrieved from cache: ".$this->counter_queries_from_cached);
	}

	/**
	 * Log an event and publish it to listeners
	 * @return bool
	 */
	private function log(string $code,string $description):bool{
		if(is_null($this->Logger)){return false;}
		$Event=new Event($this::class,$code,$description);
		return $this->Logger->publish($Event);
	}


	private function queryObjects($sql,$cache=true):array{
		$return=array();
		//$_SESSION['coordinator_logs'][]=array("log","PDO queryObjects: ".$sql);
		// check for cache
		/*if($cache){
			$return=$this->getQueryFromCache($sql);
			if($return!==false){return $return;}
		}*/
		// execute query
		try{
			$results=$this->connection_read->query($sql);
			$return=$results->fetchAll(PDO::FETCH_OBJ);
			//if(DEBUG){$_SESSION['coordinator_logs'][]=array("log","PDO queryObjects results:\n".var_export($return,true));}
		}catch(PDOException $e){
			throw StorageException::savingError("PDO queryObjects: ".$e->getMessage());  // @todo cambiare metodo?
			// log or exception?
		}
		if(!is_array($return)){$return=array();}  // verificare
		$this->counter_queries_executed++;
		//if($cache && count($return)){$this->addQueryToCache($sql,$return);}
		return $return;
	}

	public function queryUniqueObject($sql,$cache=true):object|false{
		$sql.=" LIMIT 0,1";
		//$_SESSION['coordinator_logs'][]=array("log","PDO queryUniqueObject: ".$sql);
		// check for cache
		/*if($cache){
			$return=$this->getQueryFromCache($sql);
			if($return!==false){return $return;}
		}*/
		// execute query
		try{
			$results=$this->connection_read->query($sql);
			$return=$results->fetch(PDO::FETCH_OBJ);
			//if(DEBUG){$_SESSION['coordinator_logs'][]=array("log","PDO queryUniqueObject result:\n".var_export($return,true));}
		}catch(PDOException $e){
			throw StorageException::savingError("PDO queryUniqueObject: ".$e->getMessage());  // @todo cambiare metodo?
			// log or exception?
			$return=false;
		}
		//
		$this->counter_queries_executed++;
		//if($cache && $return){$this->addQueryToCache($sql,$return);}
		return $return;
	}

	public function queryUniqueValue($sql,$cache=true){
		$sql.=" LIMIT 0,1";
		//$_SESSION['coordinator_logs'][]=array("log","PDO queryUniqueValue: ".$sql);
		// check for cache
		if($cache){
			$return=$this->getQueryFromCache($sql);
			if($return!==false){return $return;}
		}
		// execute query
		try{
			$results=$this->connection_read->query($sql);
			$return=$results->fetch(PDO::FETCH_NUM)[0];
			//if(DEBUG){$_SESSION['coordinator_logs'][]=array("log","PDO queryUniqueValue result: ".$return);}
		}catch(PDOException $e){
			throw StorageException::savingError("PDO queryUniqueValue: ".$e->getMessage());  // @todo cambiare metodo?
			$return=false;
		}
		//
		$this->counter_queries_executed++;
		//if($cache && $return){$this->addQueryToCache($sql,$return);}
		return $return;
	}

	public function queryCount($table,$where="1"){
		$sql="SELECT COUNT(*) FROM `".$table."` WHERE ".$where;
		//$_SESSION['coordinator_logs'][]=array("log","PDO queryCount: ".$sql);
		try{
			$results=$this->connection_read->query($sql);
			$return=$results->fetchColumn();
		}catch(PDOException $e){
			throw StorageException::savingError("PDO queryCount: ".$e->getMessage());  // @todo cambiare metodo?
			$return=false;
		}
		$this->counter_queries_executed++;
		return $return;
	}

	public function queryInsert(string $table,mixed $uid,array $properties){
		$return=false;
		$fields_array=array();
		$results=$this->connection_read->query("SHOW COLUMNS FROM `".$table."`");
		foreach($results->fetchAll(PDO::FETCH_OBJ) as $field){$fields_array[$field->Field]=$field;}

		$fields2_array=array();
		$results=$this->connection_read->query("SELECT * FROM `information_schema`.`columns` WHERE `table_name`='".$table."'");
		foreach($results->fetchAll(PDO::FETCH_OBJ) as $field){$fields2_array[$field->COLUMN_NAME]=$field;} // for DATA_TYPE & CHARACTER_MAXIMUM_LENGTH
		//var_dump($fields2_array);

		if(isset($fields_array['uid'])){unset($fields_array['uid']);}else{throw StorageException::cannotSaveWithoutUID();}  // @todo valutare se cambiare metodo
		$sql="INSERT INTO `".$table."` (`uid`,";
		foreach(array_keys($properties) as $key){   // remove - from keys
			if(!is_string($key) || !array_key_exists($key,$fields_array)){unset($properties[$key]);continue;}
			if(is_string($properties[$key]) && trim($properties[$key])===''){unset($properties[$key]);continue;}
			if(is_bool($properties[$key])){$properties[$key]=(int)$properties[$key];}
			$sql.="`".$key."`,";
		}

		if(!count($properties)){throw StorageException::savingError("No properties to save");}

		$sql=substr($sql,0,-1).") VALUES (:uid,";
		foreach(array_keys($properties) as $key){$sql.=":".$key.",";}
		$sql=substr($sql,0,-1).")";

		//$_SESSION['coordinator_logs'][]=array("log","PDO queryInsert: ".$sql."\n".var_export($object,true));
		try{
			$statement=$this->connection_write->prepare($sql);
			$statement->bindParam(':uid', $uid);
			foreach(array_keys($properties) as $key){
				//
				if($fields2_array[$key]->DATA_TYPE=='varchar'){
					if(!is_null($properties[$key])){
						$properties[$key]=substr($properties[$key],0,$fields2_array[$key]->CHARACTER_MAXIMUM_LENGTH);
					}
				}
				$statement->bindParam(':'.$key, $properties[$key]);
			}
			//var_dump($statement->queryString);
			//var_dump($uid);
			//var_dump($properties);
			$statement->execute();
			//$return=$this->connection_write->lastInsertId();
			$result=$statement->rowCount();

			//var_dump($result,"result");

			if($result==1){$return=true;}

		}catch(PDOException $e){
			throw StorageException::savingError("PDO queryInsert: ".$e->getMessage());
			$return=false;
		}
		$this->counter_queries_executed++;
		return $return;
	}

	public function queryUpdate(string $table,mixed $uid,array $properties){
		//var_dump($properties);
		$return=false;
		$fields_array=array();
		$results=$this->connection_read->query("SHOW COLUMNS FROM `".$table."`");
		foreach($results->fetchAll(PDO::FETCH_OBJ) as $field){$fields_array[$field->Field]=$field;}

		$fields2_array=array();
		$results=$this->connection_read->query("SELECT * FROM `information_schema`.`columns` WHERE `table_name`='".$table."'");
		foreach($results->fetchAll(PDO::FETCH_OBJ) as $field){$fields2_array[$field->COLUMN_NAME]=$field;} // for DATA_TYPE & CHARACTER_MAXIMUM_LENGTH
		//var_dump($fields2_array);

		if(isset($fields_array['uid'])){unset($fields_array['uid']);}else{throw StorageException::cannotSaveWithoutUID();}  // @todo valutare se cambiare metodo
		$sql="UPDATE `".$table."` SET ";
		//var_dump($fields_array);
		//var_dump($properties);

		foreach(array_keys($properties) as $key){   // remove - from keys
			if(!is_string($key) || !array_key_exists($key,$fields_array)){unset($properties[$key]);continue;}
			if(is_string($properties[$key]) && trim($properties[$key])===''){unset($properties[$key]);continue;}
			if(is_bool($properties[$key])){$properties[$key]=(int)$properties[$key];}
			$sql.="`".$key."`=:".$key.",";
		}

		//var_dump($properties);

		if(!count($properties)){throw StorageException::savingError("No properties to save");}

		$sql=substr($sql,0,-1)." WHERE `uid`=:uid";

		//$_SESSION['coordinator_logs'][]=array("log","PDO queryInsert: ".$sql."\n".var_export($object,true));
		try{
			$statement=$this->connection_write->prepare($sql);
			$statement->bindParam(':uid', $uid);
			foreach(array_keys($properties) as $key){
				//
				if($fields2_array[$key]->DATA_TYPE=='varchar'){
					if(!is_null($properties[$key])){
						$properties[$key]=substr($properties[$key],0,$fields2_array[$key]->CHARACTER_MAXIMUM_LENGTH);
					}
				}
				$statement->bindParam(':'.$key, $properties[$key]);
			}
			/*
			var_dump($statement->queryString,"query");
			var_dump($uid);
			var_dump($properties);
			*/
			$statement->execute();
			//$return=$this->connection_write->lastInsertId();
			$result=$statement->rowCount();

			//var_dump($result,"result");

			//if($result==1){$return=true;}
			$return=true;  // @occhio*1

		}catch(PDOException $e){
			throw StorageException::savingError("PDO queryUpdate: ".$e->getMessage());
			$return=false;
		}
		$this->counter_queries_executed++;
		return $return;
	}

	public function queryDelete(string $table,mixed $uid){
		$return=false;
		$sql="DELETE FROM `".$table."` WHERE `uid`=:uid";
		//$_SESSION['coordinator_logs'][]=array("log","PDO queryDelete: ".$sql);
		try{
			$statement=$this->connection_write->prepare($sql);
			$statement->bindParam(':uid', $uid);
			//var_dump($statement->queryString,"query");
			//var_dump($uid);

			$statement->execute();
			$result=$statement->rowCount();

			if($result==1){$return=true;}

			//$_SESSION['coordinator_logs'][]=array("warn","PDO queryDelete: ".$query->rowCount()." rows deleted");

		}catch(PDOException $e){
			throw StorageException::savingError("PDO queryDelete: ".$e->getMessage());
			$return=false;
		}
		$this->counter_queries_executed++;
		return $return;
	}



	private function getDataset(ModelInterface $Model):string{
		$dataset=$Model::$_dataset;
		if(!strlen($dataset)){throw StorageException::datasetNotDefined();} // @todo checks
		return $dataset;
	}

	private function filtersToWhere(FilterInterface $Filters){
		//var_dump($Filters);
		$conditions_parsed=' WHERE ';
		$Condition=$Filters->getCondition();
		if(is_a($Condition,Conditions::class)){
			$conditions_parsed.=$this->parseConditions($Condition);
		}elseif(is_a($Condition,Condition::class)){
			$conditions_parsed.=$this->parseCondition($Condition);
		}else{
			throw StorageException::genericException("Invalid condition class: ".$Condition::class);
		}
		//var_dump($conditions_parsed,'filter to where parsed conditions');
		return $conditions_parsed;
	}

	private function parseConditions(Conditions $Conditions):string{
		$conditions_parsed_array=array();
		/** @var Conditions|Condition $Condition */
		foreach($Conditions->getConditions() as $Condition){
			if(is_a($Condition,Conditions::class)){
				$conditions_parsed_array[]=$this->parseConditions($Condition);
			}elseif(is_a($Condition,Condition::class)){
				$conditions_parsed_array[]=$this->parseCondition($Condition);
			}else{
				throw StorageException::genericException("Invalid condition class: ".$Condition::class);
			}
		}
		return '( '.implode(' '.$Conditions->getOperator().' ',$conditions_parsed_array).' )';
	}

	private function parseCondition(Condition $Condition):string{
		$return='`'.$Condition->getProperty().'`';
		switch($Condition->getAssertion()){
			case 'isNull':$return.=" IS NULL";break;
			case 'isNotNull':$return.=" IS NOT NULL";break;
			case 'isEqualsTo':$return.=" = '".$Condition->getValue()."'";break;
			case 'isNotEqualsTo':$return.=" <> '".$Condition->getValue()."'";break;
			case 'isGreaterThan':$return.=" > '".$Condition->getValue()."'";break;
			case 'isGreaterEqualThan':$return.=" >= '".$Condition->getValue()."'";break;
			case 'isLesserThan':$return.=" < '".$Condition->getValue()."'";break;
			case 'isLesserEqualThan':$return.=" <= '".$Condition->getValue()."'";break;
			case 'isNotLike':$return.=" NOT"; // continue down
			case 'isLike':$return.=" LIKE '".str_replace('*','%',$Condition->getValue())."'";break;
			case 'isNotIn':$return.=" NOT"; // continue down
			case 'isIn':$return.=" IN ('".implode("','",$Condition->getValue())."')";break;
			case 'isNotBetween':$return.=" NOT"; // continue down
			case 'isBetween':$return.=" BETWEEN '".$Condition->getValue()[0]."' AND '".$Condition->getValue()[1]."'";break;
			default:throw StorageException::genericException("Assertion '".$Condition->getAssertion()."' is not implemented");
		}
		return $return;
	}





	public function count(ModelInterface $Model,?FilterInterface $Filters=null):int{
		$table=$this->getDataset($Model);

		$sql='SELECT COUNT(`uid`) AS `counter` FROM `'.$table.'`';

		if(!is_null($Filters)){        // @todo sistemare con prepare ecc
			$sql.=$this->filtersToWhere($Filters);
		}

		//var_dump($sql,"SQL Count Query");

		$object=$this->queryUniqueObject($sql);

		if($object===false){throw StorageException::countError();}

		return $object->counter;

	}






	public function browse(ModelInterface $Model,?FilterInterface $Filters=null,?SortingInterface $Sorting=null,?PaginationInterface $Pagination=null):array{

		$result=array();

		$table=$this->getDataset($Model);

		/*
		var_dump($Filters,'Filters');
		var_dump($Sorting,'Sorting');
		var_dump($Pagination,'Pagination');
		*/

		// @todo try

		$sql='SELECT `uid` FROM `'.$table.'`';

		// @todo mettere tutto in private function in modo da usare sia per browse che per count la query generation

		if(!is_null($Filters)){        // @todo sistemare con prepare ecc
			$sql.=$this->filtersToWhere($Filters);
		}

		if(!is_null($Sorting)){
			$sortings=array();
			foreach($Sorting->getProperties() as $property=>$method){
				$sortings[]='`'.$property.'` '.$method;
			}
			if(count($sortings)){$sql.=' ORDER BY '.implode(',',$sortings);}
		}

		if(!is_null($Pagination)){
			if($Pagination->getLimit()>0){$sql.=' LIMIT '.$Pagination->getLimit();}
			if($Pagination->getOffset()>0){$sql.=' OFFSET '.$Pagination->getOffset();}
		}

		//var_dump($sql);

		$objects=$this->queryObjects($sql);

		foreach($objects as $object){
			$result[]=$object->uid;
		}

		return $result;
	}


	public function exists(ModelInterface $Model,mixed $uid):bool{

		$table=$Model::$_dataset;

		// @todo try

		$sql="SELECT `uid` FROM `".$table."` WHERE `uid`='".$uid."'";        // @todo sistemare con prepare ecc e ' al posto di "

		$object=$this->queryUniqueObject($sql);

		if($object===false){return false;}
		if($object->uid!=$uid){return false;}    // serve?

		return true;
	}


	public function load(ModelInterface $Model,mixed $uid):ModelInterface{
		if(!$this->exists($Model,$uid)){throw StorageException::uidNotAvailable($uid);}

		$table=$Model::$_dataset;

		// @todo try

		$sql="SELECT * FROM `".$table."` WHERE `uid`='".$uid."'";        // @todo sistemare con prepare ecc e ' al posto di "

		$object=$this->queryUniqueObject($sql);
		//$object=$this->getModelFromCache($Model);
		foreach($object as $property=>$value){  // @todo chiamare setProperties($properties) e contare return
			$Model->setProperty($property,$value);
		}
		return $Model;
	}

	public function loadFromKey(ModelInterface $Model,string $key,mixed $value,mixed &$uid):ModelInterface{   // @todo valutare come sostituire facilmente com browse per evitare un metodo "duplicato" e funzionale solo per i database
		$table=$Model::$_dataset;
		// @todo try
		$sql="SELECT * FROM `".$table."` WHERE `".$key."`='".$value."'";
		//$object=$this->getModelFromCache($Model);
		$object=$this->queryUniqueObject($sql);
		if($object===false){throw StorageException::notAvailable($key,$value);}
		if(!strlen($object->uid)){throw StorageException::notAvailable($key,$value);}  // in caso eliminare anche exception
		$uid=$object->uid;  // @schifo
		foreach($object as $property=>$value){  // @todo chiamare setProperties($properties) e contare return
			$Model->setProperty($property,$value);
		}
		return $Model;
	}

	public function loadFromKeys(ModelInterface $Model,array $keys,mixed &$uid):ModelInterface{   // @todo come sopra valutare come sostituire facilmente com browse per evitare un metodo "duplicato" e funzionale solo per i database
		$table=$Model::$_dataset;
		// @todo try

		$query_where=array();
		foreach($keys as $key=>$value){
			$query_where[]="`".$key."`='".$value."'";
		}

		$sql="SELECT * FROM `".$table."` WHERE ".implode(" AND ",$query_where);

		//$object=$this->getModelFromCache($Model);
		$object=$this->queryUniqueObject($sql);
		if($object===false){throw StorageException::notAvailable('('.implode(',',array_keys($keys)).')','('.implode(',',$keys).')');}
		if(!strlen($object->uid)){throw StorageException::notAvailable('('.implode(',',array_keys($keys)).')','('.implode(',',$keys).')');}  // in caso eliminare anche exception
		$uid=$object->uid;  // @schifo
		foreach($object as $property=>$value){  // @todo chiamare setProperties($properties) e contare return
			$Model->setProperty($property,$value);
		}
		return $Model;
	}


	public function save(ModelInterface $Model):bool{

		$uid=$Model->getUid();
		if(!strlen($uid)){throw StorageException::cannotSaveWithoutUID();}

		$table=$Model::$_dataset;

		$properties=$Model->getProperties();

		// @todo try

		if($this->exists($Model,$uid)){
			$result=$this->queryUpdate($table,$uid,$properties);    // @occhio*1 che se non si modifica nulla (salvo senza apportare cambiamenti) da errore anche in caso di query ok..
		}else{
			$result=$this->queryInsert($table,$uid,$properties);
		}

		// @todo check con exists?
		return $result;

	}


	public function remove(ModelInterface $Model):bool{
		$uid=$Model->getUid();
		if(!strlen($uid)){throw StorageException::cannotRemoveWithoutUID();}
		if(!$this->exists($Model,$uid)){throw StorageException::uidNotAvailable($uid);}

		$table=$Model::$_dataset;

		$result=$this->queryDelete($table,$uid);

		return $result;
	}

}
