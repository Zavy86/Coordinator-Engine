<?php
/**
 * Session
 *
 * @package Coordinator\Engine
 * @author Manuel Zavatta <manuel.zavatta@gmail.com>
 */

namespace Coordinator\Engine\Session;

use Coordinator\Engine\Configuration\ApplicationConfiguration;
use Coordinator\Engine\Engine;
use Coordinator\Engine\Handler\Request;

final class Session implements SessionInterface{

	private string $secret;

	protected bool $valid=false;
	protected string $token='';
	protected ?string $account=null;
	protected ?string $client=null;
	protected ?int $duration;
	protected ?int $generation;
	protected ?int $expiration=null;
	protected bool $administrator=false;
	protected array $authorizations=[];
	protected array $data=[];

	public function __construct(){
		$ApplicationConfiguration=new ApplicationConfiguration('../configurations/application.json');
		$this->secret=$ApplicationConfiguration->get('secret');
		//var_dump($ApplicationConfiguration);
		$token=$this->getBearerToken();
		//var_dump($token);
		if(strlen($token)){
			// @todo check se ci sono 2 punti (o cmq se il formato è corretto)
			$this->token=$token;
			$this->loadFromBearerToken($this->token);
			$this->valid=$this->bearerTokenIsValid($this->token);
			//var_dump($this);
		}
	}

	private function checkBearerTokenFormat(string $bearer_token):bool{
		// @todo check se ci sono 2 punti (@todo o cmq se il formato è corretto)
		if(!strlen($bearer_token)){return false;}
		$first_dot=strpos($bearer_token,".");
		if($first_dot===false){return false;}
		$second_dot=strpos($bearer_token,".",$first_dot);
		if($second_dot===false){return false;}
		return true;
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
		if(!$this->checkBearerTokenFormat($bearer_token)){return;}
		$payload=json_decode(base64_decode(explode(".",$bearer_token)[1]),true);
		//var_dump($payload);
		$this->account=$payload['account'];
		$this->client=$payload['client'];
		$this->duration=$payload['duration'];
		$this->generation=$payload['generation'];
		$this->expiration=$payload['expiration'];
		$this->administrator=$payload['administrator'];
		$this->authorizations=$payload['authorizations'];
		$this->data=$payload['data'];
		//var_dump($this);
	}

	private function bearerTokenIsValid(string $bearer_token):bool{
		if(!$this->checkBearerTokenFormat($bearer_token)){return false;}
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
		$signature=hash_hmac("SHA256",$base64_url_header.".".$base64_url_payload,$this->secret,true);  /* @todo unire in funcion ripetuto con sopra */
		$base64_url_signature=$this->base64url_encode($signature);
		// verify it matches the signature provided in the jwt
		$is_signature_valid=($base64_url_signature===$signature_provided);
		// checks
		if($is_token_expired||!$is_signature_valid){
			return false;
		}else{
			return true;
		}
	}

	private function base64url_encode(string $data):string{    // @todo rendere generiche?
		return rtrim(strtr(base64_encode($data),"+/","-_"),"=");
	}

	public function invalidate(){
		$this->valid=false;
	}

	public function validate(string $account,string $client,int $duration,bool $administrator,array $authorizations,array $data=[]):bool{

		if($duration<60){$duration=60;}
		if($duration>(60*60*24*10)){$duration=(60*60*24*10);}

		$generation=time();
		$expiration=($generation+$duration);

		$headers=array(
			"alg"=>"HS256",
			"typ"=>"JWT"
		);
		$payload=array(
			"account"=>$account,
			"client"=>$client,
			"duration"=>$duration,
			"generation"=>time(),
			"expiration"=>$expiration,
			"administrator"=>$administrator,
			"authorizations"=>$authorizations,
			"data"=>$data
		);
		//var_dump($payload);
		$this->valid=true;
		$this->token=$this->generate_jwt($headers,$payload);
		$this->account=$account;
		$this->client=$client;
		$this->duration=$duration;
		$this->generation=$generation;
		$this->expiration=$expiration;
		$this->administrator=$administrator;
		$this->authorizations=$authorizations;
		$this->data=$data;

		return true;

	}

	private function generate_jwt(array $headers,array $payload,):string{
		$headers_encoded=$this->base64url_encode(json_encode($headers));
		$payload_encoded=$this->base64url_encode(json_encode($payload));
		$signature=hash_hmac("SHA256",$headers_encoded.".".$payload_encoded,$this->secret,true);
		$signature_encoded=$this->base64url_encode($signature);
		return $headers_encoded.".".$payload_encoded.".".$signature_encoded;
	}

	public function checkAuthorization(string $authorization):bool{
		return true;
	}

	// @todo mettere nell'interfaccia?
	public function isValid():bool{return $this->valid;}
	public function isAdministrator():bool{return $this->administrator;}
	public function getToken():?string{return $this->token??null;}
	public function getAccount():?string{return $this->account??null;}
	public function getClient():?string{return $this->client??null;}
	public function getDuration():?int{return $this->duration??null;}
	public function getGeneration():?int{return $this->generation??null;}
	public function getExpiration():?int{return $this->expiration??null;}
	public function getRemaining():?int{return isset($this->expiration)?($this->expiration-time()):null;}
	public function getAuthorizations():array{return $this->authorizations;}
	public function getData():array{return $this->data;}

	public function debug():array{
		return array(
			'valid'=>$this->isValid(),
			'token'=>$this->getToken(),
			'account'=>$this->getAccount(),
			'client'=>$this->getClient(),
			'duration'=>$this->getDuration(),
			'generation'=>$this->getGeneration(),
			'expiration'=>$this->getExpiration(),
			'remaining'=>$this->getRemaining(),
			'administrator'=>$this->isAdministrator(),
			'authorizations'=>$this->getAuthorizations(),
			'data'=>$this->getData()
		);
	}

}
