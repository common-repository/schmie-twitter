<?php

class To_Pingfm{
/**
@date: 12.03.10
@author: schmiddi, schmiddim@gmx.at, twitter:derZwitschernde ;)
@desc:Nachrichten an Ping.fm senden. Die Nachricht wird im Konstruktor  übergeben bla

die Mail abgesetzt werden
public function __construct($message)		constructor with the messagen (>180 will be cut)
public function talk_via_curl($usr, $pass)	tweets via curl
public function is_twitter_reachable()		are you able to connect to twitter?

*/

	public $err_arr;	//array with all the errors
	public $msg;

	public function __construct($message){
		$this->err_arr =array();
		if (strlen($message)>140) {
			$this->add_error('construct: Warning more than 140 chars - will be cut');
			$this->msg= substr($message,0,139);
			
		}else {	$this->msg=$message;}
	}//function


	public function talk_via_curl($user_app_key, $api_key){
		if (empty($user_app_key)) {
			$this->add_error('pingfm_via_curl: empty login');
			return;
		}//fi
		$message = $this->msg;
		// The Identica API Adress

		$url = 'http://api.ping.fm/v1/user.post';
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, "$url");
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_POST, 1);

		$postfields['body']=$message;
		$postfields['user_app_key']=$user_app_key;
		$postfields['api_key']=$api_key;
		$postfields['post_method']='default';
		$postfields['status']='';

		curl_setopt($curl_handle, CURLOPT_POSTFIELDS,$postfields);


	


		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);

#echo $buffer;
	if(preg_match('/<error>Could not authenticate you./',$buffer))
			$this->add_error('tweet_via_curl: coult not authenticate you');
		
	}//function


	public function is_twitter_reachable(){
	/** is your server able to talk to twitter? **/
		$hostname='identi.ca';		
		$ip = gethostbyname('identi.ca');
		if ($hostname == $ip) {
			$this->add_error("unable to reach identi.ca");
			
			return false;
		}else {
			
			return true;
		}//fi

	}//function

	/**************************Error Handling **/
	private function add_error($msg) {
		array_push($this->err_arr,$msg);
	}#function

	public function get_errors() {
		foreach($this->err_arr as $error)
			echo $error."<p>";
	}//function
	/**************************Error Handling **/

}//class

/***********TESTING*******/
#echo "bit.ly";
#$tw = new To_Pingfm("php says hello ... on ping.fm again!");

#$tw->talk_via_curl($usr_api, $api);

#$tw->is_twitter_reachable();
#$tw->get_errors();
?>
