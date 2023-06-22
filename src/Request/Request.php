<?php
/**
 * Request
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Request;

use Coordinator\Engine\Filter\Filter;
use Coordinator\Engine\Filter\FilterInterface;
use Coordinator\Engine\Object\ObjectInterface;
use Coordinator\Engine\Pagination\Pagination;
use Coordinator\Engine\Pagination\PaginationInterface;
use Coordinator\Engine\Sorting\Sorting;
use Coordinator\Engine\Sorting\SortingException;
use Coordinator\Engine\Sorting\SortingInterface;

final class Request implements RequestInterface{

	protected string $address;
	protected string $method;
	protected string $uri;
	protected array $query;
	protected array $body;

	public function __construct(){
		$this->setAddress();
		$this->setMethod();
		$this->setUri();
		$this->setQuery();
		$this->setBody();
	}

	private function getDecodedRequest():string{
		return str_replace('//','/',rawurldecode(filter_var(trim($_SERVER["REQUEST_URI"]),FILTER_SANITIZE_URL)));
	}

	private function setAddress():void{
		$this->address=$_SERVER['REMOTE_ADDR'];
	}

	private function setMethod():void{
		$method=strtoupper($_SERVER['REQUEST_METHOD']);
		if(!in_array($method,array("GET","POST","PUT","DELETE","PATCH","HEAD","OPTIONS"))){throw RequestException::methodNotAllowed($method);}  // @todo fare ENUM
		$this->method=$method;
	}

	private function setUri():void{
		$this->uri=explode("?",$this->getDecodedRequest())[0];
	}

	private function setQuery():void{
		$this->query=array();
		$uri_exploded=explode("?",$this->getDecodedRequest());
		if(isset($uri_exploded[1])){
			foreach(explode("&",$uri_exploded[1]) as $query){
				$query_exploded=explode("=",$query);
				$this->query[$query_exploded[0]]=$query_exploded[1];
			}
		}
	}

	private function setBody():void{
		if(isset($_POST) && count($_POST)){$body=$_POST;}
		else{$body=json_decode(file_get_contents("php://input"),true);}
		if(is_null($body)){$body=array();}
		if(!is_array($body)){$body=array($body);}
		$this->body=$body;
	}

	final public function getAddress():string{return $this->address;}
	final public function getMethod():string{return $this->method;}
	final public function getUri():string{return $this->uri;}
	final public function getQuery():array{return $this->query;}
	final public function getBody():array{return $this->body;}

	final public function hasBody():bool{return count($this->body);}

	final public function getObject(string $class):ObjectInterface{
		$interfaces=class_implements($class);
		if(!is_array($interfaces) || !in_array(ObjectInterface::class,$interfaces)){
			throw RequestException::objectTypeMismatch($class);
		}
		return new $class($this->getBody());
	}

	public function getFilter():?FilterInterface{
		if(!$this->hasBody()){return null;}
		$body_properties=$this->getBody();
		if(!array_key_exists('Filter',$body_properties)){return null;}
		return Filter::buildFromArray($body_properties['Filter']);
	}

	public function getSorting(?SortingInterface $defaultSorting=null):?SortingInterface{
		if(!$this->hasBody()){return $defaultSorting;}
		$body_properties=$this->getBody();
		if(!array_key_exists('Sorting',$body_properties)){return $defaultSorting;}
		$sorting_array=[];
		foreach($body_properties['Sorting'] as $sorting){
			if(!array_key_exists('property',$sorting) || !array_key_exists('method',$sorting)){continue;}
			$sorting_array[$sorting['property']]=$sorting['method'];
		}
		if(!count($sorting_array)){SortingException::parsingError();}
		return new Sorting($sorting_array);
	}

	public function getPagination(int $limitDefault=20,int $limitMax=100):?PaginationInterface{
		$query=$this->getQuery();
		$limit=(int)($query['limit']??0);
		$offset=(int)($query['offset']??0);
		if(!is_int($limit)||$limit<1){$limit=$limitDefault;}
		if($limit>$limitMax){$limit=$limitMax;}
		if(!is_int($offset)){$offset=0;}
		return new Pagination($limit,$offset);
	}

	public function getSearch():?string{
		$query=$this->getQuery();
		if(!array_key_exists('search',$query)){return null;}
		$search=trim(htmlspecialchars($query['search']));
		if(!strlen($search)){return null;}
		return $search;
	}

	public function debug():array{
		return array(
			'class'=>$this::class,
			'address'=>$this->getAddress(),
			'method'=>$this->getMethod(),
			'uri'=>$this->getUri(),
			'query'=>$this->getQuery(),
			'body'=>$this->getBody(),
		);
	}

}
