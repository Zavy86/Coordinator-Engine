<?php
/**
 * Session
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Session;

use Coordinator\Engine\Engine;
use Coordinator\Engine\Handler\Request;

final class Session implements SessionInterface{

	private const SECRET_KEY="@generaresecretkey";              // @todo spostare nelle configurazioni dell'applicazione
	private const MAX_DURATION=60*60*24;                        // @todo spostare nelle configurazioni dell'applicazione

	protected bool $valid=false;
	protected string $token='';
	protected string $address;
	protected ?string $username=null;
	protected ?string $client=null;
	protected ?int $duration;
	protected ?int $generation;
	protected ?int $expiration=null;

	public function __construct(){
		$this->setAddress();
		$token=$this->getBearerToken();
		//var_dump($token);
		if(strlen($token)){
			// @todo check se ci sono 2 punti (o cmq se il formato è corretto)
			$this->token=$this->getBearerToken();
			$this->loadFromBearerToken($this->token);
			$this->valid=$this->bearerTokenIsValid($this->token);
			//var_dump($this);
		}
	}

	private function setAddress(){
		$this->address=$_SERVER['REMOTE_ADDR'];
	}

	private function getBearerToken():string{  // @todo verificare per bene copiato spudoratamente da stackoverflow
		$headers='';
		if(isset($_SERVER['Authorization'])){
			$headers=trim($_SERVER['Authorization']);
		}elseif(isset($_SERVER['HTTP_AUTHORIZATION'])){
			// Nginx or fast CGI
			$headers=trim($_SERVER['HTTP_AUTHORIZATION']);
		}elseif(function_exists('apache_request_headers')){
			$requestHeaders=apache_request_headers();
			// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
			$requestHeaders=array_combine(array_map('ucwords',array_keys($requestHeaders)),array_values($requestHeaders));
			//print_r($requestHeaders);
			if(isset($requestHeaders['Authorization'])){
				$headers=trim($requestHeaders['Authorization']);
			}
		}
		// Get the access token from the header
		if(!empty($headers)){
			if(preg_match("/Bearer\s(\S+)/",$headers,$matches)){
				return $matches[1];
			}
		}
		return '';
	}

	private function loadFromBearerToken(string $bearer_token){
		if(!strlen($bearer_token)){return;}
		// @todo check se ci sono 2 punti (o cmq se il formato è corretto)
		$payload=json_decode(base64_decode(explode(".",$bearer_token)[1]));
		//var_dump($payload);
		$this->username=$payload->username;
		$this->client=$payload->client;
		$this->duration=$payload->duration;
		$this->generation=$payload->generation;
		$this->expiration=$payload->expiration;
		//var_dump($this);
	}

	private function bearerTokenIsValid(string $bearer_token,string $secret=self::SECRET_KEY):bool{
		if(!strlen($bearer_token)){return false;}
		// @todo check se ci sono 2 punti (o cmq se il formato è corretto)
		$tokenParts=explode(".",$bearer_token);
		$header=base64_decode($tokenParts[0]);
		$payload=base64_decode($tokenParts[1]);
		$signature_provided=$tokenParts[2];
		// check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
		$expiration=json_decode($payload)->expiration;
		$is_token_expired=($expiration-time())<0;
		// build a signature based on the header and payload using the secret
		$base64_url_header=$this->base64url_encode($header);
		$base64_url_payload=$this->base64url_encode($payload);
		$signature=hash_hmac("SHA256",$base64_url_header.".".$base64_url_payload,$secret,true);  /* @todo unire in funcion ripetuto con sopra */
		$base64_url_signature=$this->base64url_encode($signature);
		// verify it matches the signature provided in the jwt
		$is_signature_valid=($base64_url_signature===$signature_provided);
		// verify if address match
		$address=json_decode($payload)->address;
		$is_address_match=($address==$this->getAddress());
		// checks
		if($is_token_expired||!$is_signature_valid||!$is_address_match){
			return false;
		}else{
			return true;
		}
	}

	private function base64url_encode(string $data):string{    // @todo rendere generiche?
		return rtrim(strtr(base64_encode($data),"+/","-_"),"=");
	}





  // @todo migliorare sperando in più funzioni
	public function authenticate(string $username,string $password,string $client,string $secret,int $duration):bool{

		$authentication_success=false;

		// manual checks            @ todo fare classe per gestione autenticazione da config, da database, da ldap ecc...
		if($username=='administrator'){
			if(Engine::checkAdministratorPassword($password)){
				$authentication_success=true;
			}
		}

		// check client and secret

		/*
		 * username and password or ldap
		if(SystemTokenModel::exists($body['token'])){
			$SystemToken=SystemTokenModel::get($body['token']);
			if($SystemToken->secret===md5($body['secret'])){
				$authentication_success=true;
			}
		}*/

		if(!$authentication_success){
			return false;
		}

		if($duration<60 || $duration>$this::MAX_DURATION){
			$duration=$this::MAX_DURATION;
		}

		$generation=time();
		$expiration=($generation+$duration);

		$headers=array(
			"alg"=>"HS256",
			"typ"=>"JWT"
		);
		$payload=array(
			"address"=>$this->getAddress(),
			"username"=>$username,
			"client"=>$client,
			"duration"=>$duration,
			"generation"=>time(),
			"expiration"=>$expiration
		);
		//var_dump($payload);
		$this->valid=true;
		$this->token=$this->generate_jwt($headers,$payload);
		$this->username=$username;
		$this->client=$client;
		$this->duration=$duration;
		$this->generation=$generation;
		$this->expiration=$expiration;

		return true;

	}

	private function generate_jwt(array $headers,array $payload,string $secret=self::SECRET_KEY):string{
		$headers_encoded=$this->base64url_encode(json_encode($headers));
		$payload_encoded=$this->base64url_encode(json_encode($payload));
		$signature=hash_hmac("SHA256",$headers_encoded.".".$payload_encoded,$secret,true);
		$signature_encoded=$this->base64url_encode($signature);
		return $headers_encoded.".".$payload_encoded.".".$signature_encoded;
	}



								public function getPermissions():array{
									return array();
								}

								public function checkAuthorization(string $authorization):bool{
									return true;
								}



  // @todo mettere nell'interfaccia?
	public function isValid():bool{return $this->valid;}
	public function getToken():?string{return $this->token??null;}
	public function getAddress():?string{return $this->address??null;}
	public function getUsername():?string{return $this->username??null;}
	public function getClient():?string{return $this->client??null;}
	public function getDuration():?int{return $this->duration??null;}
	public function getGeneration():?int{return $this->generation??null;}
	public function getExpiration():?int{return $this->expiration??null;}
	public function getRemaining():?int{return isset($this->expiration)?($this->expiration-time()):null;}

	public function debug():array{
		return array(
			'valid'=>$this->isValid(),
			'token'=>$this->getToken(),
			'address'=>$this->getAddress(),
			'username'=>$this->getUsername(),
			'client'=>$this->getClient(),
			'duration'=>$this->getDuration(),
			'generation'=>$this->getGeneration(),
			'expiration'=>$this->getExpiration(),
			'remaining'=>$this->getRemaining()
		);
	}

}