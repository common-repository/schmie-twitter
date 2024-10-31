<?php 
require_once('AdtOauth.php');
/**
 * @author schmiddi
 * @version 0.1
 * @desc handles oauth stuff ( request & access the token + access on the api )
 *@todo implement other signature methods
 *@todo it should look a little bit nicer:)
 */
class OAuth{
	private $oauthInfo;		//HEy here's the Adt. Save your settings my friend:)	
	private $tokenSet=false;
	public function isTokenSet(){	return $this->tokenSet;}	
	public function getToken(){return $this->oauthInfo->oauth_token;
	
	}	
	public function getTokenSecret(){return $this->oauthInfo->oauth_token_secret;}	
	private $method="POST";	//POST || GET?		
	/**
	 * @desc setting UP Request Method
	 * @todo only used for creating the signature. Implement the stuff in the curl call. 
	 * @param String $method 
	 */
	public function setMethod($method){		$this->method=$method;	}
	
	
	
	/**
	 * @desc:	The method makes the POST Request.
	 * 			At first it generates a valid POST Header
	 * 			checks if there are something for the body and returns the 
	 * 			response as a String
	 * @todo Oauth realm is static, implement a GET Request (if neccessary) 
	 * @param Hash $params Parameters for the Header 
	 * @param String $url: the url for the request
	 * @param String $payload: a string with urlencoded values like key=value, key2=value2
	 * @return the Post response as string
	 */
	public function makeRequest($params, $url, $payload=''){
		foreach ($params as $key =>$value)
			$vals[]= "$key=\"$value\"";
			$vals = implode($vals, ', ');

		
		$headers=array("Authorization:OAuth realm=\"\", $vals");
#		print_r($headers);
	#	foreach ($headers as $header)
	#		echo "<p>$header<p>";
		$ch = curl_init();	
		curl_setopt($ch, CURLOPT_URL,  $url);
		curl_setopt($ch, CURLOPT_POST, true);		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	#    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		/** 1und1 workaround **/
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		
		if ($payload!=''){			
			 curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		}
		
		
		$response = curl_exec($ch);
		
		#echo "<p>Returncode:".  curl_getinfo($ch,CURLINFO_HTTP_CODE). '<p>'; 
		curl_close($ch);
	#echo $response;
		return $response;
	}//function
	
	/**
	 * 
	 * @desc For debugging. The Method generates a curl command for your Commandline. 
	 * 		Remember: if your making a Request, 
	 * 		the generated Command won't work because of the same nonce 
	 * @param Hash the Arguments for the Requests
	 * @param String the Url
	 * @return a curl Command with arguments for the commandline
	 */
	public function generateCurlCommand($params, $url){
		//generates a string for your commandline:)
		$vals=array();
		foreach ($params as $key =>$value)
			$vals[]= "$key=\"$value\"";
			$vals = implode($vals, ', ');
		return "curl -v -X POST -H 'Authorization:OAuth realm=\"\", $vals' $url";
	}//generateCurlCommand
	
	/**
	 *@desc Generates a call for http://term.ie/oauth/example/ A good way to check 
	 *the signature method
	 * @see http://term.ie/oauth/example/
	 * @param Hash with your Arguments
	 * @param String the url
	 * @return a link to the Url with the passed Values as Get Parameter
	 */
	public function generateDebugUrl($params,$url){
		//for Debug purpose (@visit:http://term.ie/oauth/example/)
		$str=array();
		foreach ($params as $key =>$value)
			$str[].="$key=$value";
			return $url.'?'.implode($str,'&');
		
	}//generateUrl	
	
	/**
	 * @desc default constructor
	 * @param AdtOauthInfo $adtOauthInfo an abstract datatype, with the required data for oauth calls
	 */
	public function __construct(AdtOauthInfo $adtOauthInfo){
		$this->oauthInfo = $adtOauthInfo;
		
		$this->method = $adtOauthInfo->method;
	#	$this->oauthInfo->setFunctionForGenerateNonce($this->generateNonce());
		
	}
	
	 
	/**
	 * @todo need it?
	 */
	private function generateNonce(){
		return md5(microtime(). mt_rand());
	}//function
	
	/**
	 * @todo need it?
	 */
	private	function generateTimeStamp(){	
		return time(); 
	}//function

	
	/**
	 * @todo really need it? copy it to Adt Oauth?
	 */
	private function getDefaultParameter(){
			$arguments = array(
			'oauth_version' => $this->oauthInfo->oauth_version,
			'oauth_signature_method' =>$this->oauthInfo->oauth_signature_method,
			'oauth_consumer_key' =>$this->oauthInfo->oauth_consumer_key,
			'oauth_timestamp' =>$this->generateTimeStamp(),
			'oauth_nonce' =>$this->generateNonce(),	
	 		'oauth_callback'=>$this->oauthInfo->oauth_callback
		);
		if ($this->oauthInfo->oauth_token !="")
			$arguments['oauth_token'] = $this->oauthInfo->oauth_token;
		if ($this->oauthInfo->oauth_verifier!='')
			$arguments['oauth_verifier'] = $this->oauthInfo->oauth_verifier;
		return $arguments;
	}//function

		public function AccessApi($payload, $endpoint){
			$arguments = $this->getDefaultParameter();
		
			$arguments['oauth_token'] =$this->oauthInfo->oauth_token;
			$arguments['oauth_token_secret'] =$this->oauthInfo->oauth_token_secret;
		
			$postfields = "";
			foreach ($payload as $key =>$value){
				$arguments[$key] = $value;
				$postfields.="$key=$value";		
			}
			
		#	echo $postfields;
			ksort($arguments);
			$arguments= $this->signRequest($arguments,$endpoint);
						
			$response= $this->makeRequest($arguments, $endpoint,$postfields);
		#	echo $response;				
			$values= $this->evaluateResponse($response);//fetch data from the response
	}

	public function requestAccessToken(){
			
		$arguments = $this->oauthInfo->getParameterArray();
		$values  =array();		
		foreach ($this->oauthInfo->requiredForAccessToken as $field)
			$values[$field ] = $arguments[$field];
		
		$arguments= $this->signRequest($values, $this->oauthInfo->endpoint_access_token);
		$response= $this->makeRequest($arguments, $this->oauthInfo->endpoint_access_token);

		$responses = $this->regexResponse($response);		
		
		if(!empty($responses['oauth_token'])){
			$this->oauthInfo->oauth_token = $responses['oauth_token'];
			$this->oauthInfo->oauth_token_secret = $responses['oauth_token_secret'];
			$this->oauthInfo->screen_name = $responses['screen_name'];			
			return $this->oauthInfo;
		}
		return null;
	
	}//function
	 
	public function requestToken(){		
		$arguments = $this->oauthInfo->getParameterArray();		
		$values  =array();		
		foreach ($this->oauthInfo->requiredForRequestToken as $field){
			$values[$field ] = $arguments[$field];
		}
		$arguments= $this->signRequest($values, $this->oauthInfo->endpoint_request_token);
		$response= $this->makeRequest($arguments, $this->oauthInfo->endpoint_request_token);			
		
		$responses= $this->regexResponse($response);
		
		if ($responses !=NULL && $response['oauth_callback_confirmed'] ==true){
			$this->oauthInfo->oauth_token = $responses['oauth_token'];
			$this->oauthInfo->oauth_token_secret= $responses['oauth_token_secret'];

		}else {
			//@todo:errhandling
			echo '<h1>something went wrong on REQUEST :(</h1>';
			
			/** output the requestvars **/
			echo "<h4>my call:</h4>";
			
			print_r($arguments);
			$hostname='twitter.com';		
			$ip = gethostbyname('twitter.com');
			if ($hostname == $ip) {
				echo "<h4>unable to connect to Twitter! Probably your Hoster blocks Twitter</h4>";			
			}
			
			/**is Twitter reachable?????*/
			
			if (!function_exists('md5')){
				echo "<h4>Function md5 does not exists.</h4>";	
			}
			/** curl() exists? **/
			if (!function_exists('curl')){
				echo "<h4>Your curl Extension is missing</h4>";
			}
			
		}//else
			
		return $this->oauthInfo;
		
	}//function

	/*
	 * returns an array with response values
	 * @return: array[key] = value
	 */
	private function regexResponse($response){
		$retval = array();
		$arr = explode('&', $response);
		if (!empty($arr)){
			foreach ($arr as $item){	
		#		echo "<h5>$item</h5>";						
				$key = preg_split('/=/', $item);	
		#		echo "<h4>{$key[0]} value {$key[1]}</h4>";				
				$retval[$key[0]] = $key[1];
			
			}//each		
			return $retval;
		}//fi
		return null;
	}//function
	/***
	 * @todo need it?
	 */
	private function evaluateResponse( $response,   $expectedValues=''){
		$arr = explode('&' , $response);
		$responseValues =array();
 
		return $responseValues;
		
		#			
	}//function
	

	
	/********
	 * @todo a bunch of redundant shit
	 */
	


	private function makeSig($arguments, $baseUrl){
		ksort($arguments);
		$sig="{$this->method}&".$this->oauth_urlencode($baseUrl)."&";	
	
		foreach($arguments as $key =>$value)
			$pieces[] = $this->oauth_urlencode($key."=". $value);
		$sig.=implode($pieces,$this->oauth_urlencode("&"));
	
		return $sig;
	}//function		
	/**
	 * 
	 * @desc decodes the signature with the oauth_consumer_secret and oauth_token_secret
	 * @param signature
	 * @return the siganture
	 */
	
	public function signSignature($signature){
		if ($this->oauthInfo->oauth_consumer_secret=="")
			return;
	 
		$key = $this->oauthInfo->oauth_consumer_secret . '&'. $this->oauthInfo->oauth_token_secret;
		
		return  $this->oauth_urlencode(base64_encode(hash_hmac('sha1', $signature, $key, true)));
	}
	
	public function signRequest( $arguments, $endpoint){
		
		
		$arguments= $this->encodeArguments($arguments);
		$signature = $this->makeSig($arguments, $endpoint);
		$arguments['oauth_signature'] = $this->signSignature($signature);	
		ksort($arguments);
		return $arguments;
	}
	
	private function encodeArguments($arguments){
		//hey encodes every array item. 
		ksort($arguments); 
		
		foreach ($arguments as $key=>$value)
			$arguments[$key] =  $this->oauth_urlencode($value);			
		return $arguments;
	}//function 
	

	/**
	 * @desc  decodes a string according to RFC1738 incl. the tilde
	 * @param: String $string:a string to decode 
	 */
	public function oauth_urlencode ( $string ){
    	return str_replace('%7E', '~', rawurlencode($string));
	}	
}//class


?>