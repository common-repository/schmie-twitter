<?php

class To_Twitter{
	
	private $msg;
	private $length =140;
	private $oauth;
	public function __construct($message, OAuth $oauth){
		$this->oauth = $oauth;
		
		if (strlen($message)>$this->length){
			$this->msg = substr($message,0, $this->length-1);	
		}else {
			$this->msg = $message;
		}
	
	}//function
	
	public function tweet_via_curl(){
		$endpoint ='http://api.twitter.com/1/statuses/update.json';
		$auth=$this->oauth;
		$payload = array ("status"=> $this->msg);
		$auth->accessApi($payload, $endpoint);
	}
	
	
	
}//class

/*
$endpoint ='http://api.twitter.com/1/statuses/update.json';
$auth=new OAuth($oauthInfo);
$payload = array ("status"=>"awawei?:)");
$auth->accessApi($payload, $endpoint);
 */

?>
