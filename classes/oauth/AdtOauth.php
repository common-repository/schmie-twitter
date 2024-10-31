<?php
/**
 * @author schmiddi
 * @version 0.1
 * @desc Abstract Datatype for my OAuth Class. Also handles some generators
 * @todo Method generateDefaultArguments isn't nice. Some Blobs in there :(
 */
class AdtOauthInfo{
	public $oauth_version='1.0', $oauth_signature_method='HMAC-SHA1';
	public $oauth_consumer_key, $oauth_consumer_secret;
	public $oauth_token='', $oauth_token_secret='';
	
	public $oauth_verifier='';
	public $endpoint_request_token, $endpoint_access_token, $endpoint_authorize_token;
	public $oauth_callback='oob';
	
	
	public $method="POST";
	//very usefull, if the screen_name is set, you know, that you've got an access token:)
	public $screen_name ='';	
	public function isAuthorized(){
		if ($screen_name!='')
			return true;
		return false;
	}
 
	public $expectedValuesForRequestToken = array(
	'oauth_token',
	'oauth_token_secret',
	'oauth_callback_confirmed=true'
	);
	

	public $requiredForRequestToken = array(
		'oauth_callback',
		'oauth_consumer_key',
		'oauth_nonce',
		'oauth_signature_method', 
		'oauth_timestamp',
		'oauth_version'		
	);
	public $requiredForAccessToken = array(
		'oauth_callback',
		'oauth_consumer_key',
		'oauth_nonce',
		'oauth_signature_method',
		'oauth_token',
		'oauth_timestamp',
		'oauth_verifier',
		'oauth_version'
	
	
	);
	//--------------------------------------------------
		
	public function getParameterArray(){
		$arguments = array(
			'oauth_version' => $this->oauth_version,
			'oauth_signature_method' =>$this->oauth_signature_method,
			'oauth_consumer_key' =>$this->oauth_consumer_key,
			'oauth_timestamp' =>$this->generateTimeStamp(),
			'oauth_nonce' =>$this->generateNonce(),	
	 		'oauth_callback'=>$this->oauth_callback
		);
		if ($this->oauth_token !="")
			$arguments['oauth_token'] = $this->oauth_token;
		if ($this->oauth_verifier!='')
			$arguments['oauth_verifier'] = $this->oauth_verifier;
		ksort($arguments);
		return $arguments;
	
		
	}//function
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

	/** 
	 * @desc: generates a link that directs the User to the Service Provider (e.g to Step C in the Oauth Authentication Flow
	 * @return String url. the url to the Service Provider
	 */
	public function generateAuthorizeUrl($oauth_token=''){
	
		$url= $this->endpoint_authorize;
		return"$url?oauth_token={$this->oauth_token}";
	
	}//function
	
	//--------------------------------------------------
	private function generateNonce(){
		return md5(microtime(). mt_rand());
	}//function
	
	private	function generateTimeStamp(){	
		return time(); 
	}//function
	

	public function __toString(){
		return "
		<p>
			Token       : {$this->oauth_token}<p>
			Token Secret: {$this->oauth_token_secret}<p>
			Verfifier	: {$this->oauth_verifier}<p>
			ScreenName: : {$this->screen_name}<p>
		
		";
	}//function
}//class

?>