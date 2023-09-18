<?php
/**
 * Client
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Client;

class Client implements ClientInterface{

	public function __construct(
		private string $base_url,
		private ?string $token=null
	){}

	final public function getToken():?string{
		return $this->token;
	}

	final public function setToken(?string $token):void{
		$this->token=$token;
	}

	final public function resetToken():void{
		$this->token=null;
	}

	public function get(string $url):mixed{
		return $this->execute('GET',$url);
	}

	public function post(string $url,mixed $body=null):mixed{
		return $this->execute('POST',$url,$body);
	}

	public function put(string $url,mixed $body=null):mixed{
		return $this->execute('PUT',$url,$body);
	}

	public function patch(string $url,mixed $body=null):mixed{
		return $this->execute('PATCH',$url,$body);
	}

	public function delete(string $url):mixed{
		return $this->execute('DELETE',$url);
	}

	protected function execute(string $method,string $url,mixed $body=null):mixed{
		$curl=curl_init();
		if($curl===false){throw new \Exception('failed to initialize curl');}
		curl_setopt_array($curl,array(
			CURLOPT_HTTPHEADER=>array('Content-Type:application/json'),
			CURLOPT_RETURNTRANSFER=>true,
			CURLOPT_ENCODING=>'',
			CURLOPT_MAXREDIRS=>10,
			CURLOPT_TIMEOUT=>0,
			CURLOPT_FOLLOWLOCATION=>true,
			CURLOPT_HTTP_VERSION=>CURL_HTTP_VERSION_1_1
		));
		curl_setopt($curl,CURLOPT_URL,$this->base_url.$url);
		curl_setopt($curl,CURLOPT_CUSTOMREQUEST,$method);
		if($body){curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($body));}
		if($this->token){curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type:application/json','Authorization: Bearer '.$this->token));}
		$response=json_decode(curl_exec($curl));
		if($response===false || $response===null || !isset($response->error)){throw new \Exception(curl_error($curl),curl_errno($curl));}
		curl_close($curl);
		if($response->error==true){
			if($response->errors[0]->code=='authenticationInvalid'){throw ClientException::sessionExpired();}
			throw ClientException::resultErrors($response->errors);
		}
		return $response->data;
	}

	public function debug():array{
		return array(
			'class'=>$this::class,
			'token'=>$this->token
		);
	}

}
