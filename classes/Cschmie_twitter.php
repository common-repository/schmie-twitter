<?php
/****
 * class for handling the Plugin functions
  *
 */

class CSchmie_twitter {
	#private $avoidShortener;
	
	public function getAdtOauthInfo($service){
		if ($service =='twitter'){
			$oauthInfo = new AdtOauthInfo();
			$oauthInfo->oauth_consumer_key = '7ORCIeptKqcgPmt8Lcpgpw';
			$oauthInfo->oauth_consumer_secret = '0cN8nu7TF1BijQHOco7VyROoGJ92vV0LMUgSEXZggk';
			$oauthInfo->endpoint_request_token = 'https://api.twitter.com/oauth/request_token';
			$oauthInfo->endpoint_access_token = 'https://api.twitter.com/oauth/access_token';
			$oauthInfo->endpoint_authorize='https://api.twitter.com/oauth/authorize';
			$oauthInfo->screen_name = get_option('schmie_tw_oauth_screen_name');
			$oauthInfo->oauth_token = get_option('schmie_tw_oauth_token');
			$oauthInfo->oauth_token_secret =  get_option('schmie_tw_oauth_token_secret');
			$oauthInfo->oauth_verifier = get_option('schmie_tw_verifer');
			return $oauthInfo;
		}//fi
	}//function
	/******************** VARS *****/
	public $err_arr;	//array with all the errors
	
	
	public function __construct(){		
	
		$this->err_arr =array();
	
		
	}//function
	/******************** VARS *****/
	public function format_string($title,$blog, $date, $new_url, $message) {
		/*format the status
		possible strings
		%title		post title	$title
		%blog		blog titel 	$blog 
		%date		post date	$date
		%url		post url	$new_url
	*/
		#echo $new_url;
		$message=str_replace('%title',$title,$message);
		$message=str_replace('%blog',$blog,$message);
		$message=str_replace('%date',$date,$message);
		$message=str_replace('%url',$new_url,$message);
		return $message;	
	}//format_string
	
	public function publish_message($msg) {
		//string is important, (varchar row in the DATABASE)


/*
#echo "bit.ly";
#$tw = new To_Pingfm("php says hello ... on ping.fm again!");

#$tw->talk_via_curl("user_api", 'api_key');

#$tw->is_twitter_reachable();
#$tw->get_errors();

*/
$option ='schmie_tw_select_service_pingfm';
		if (get_option($option)==$option){ // == 'schmie_tw_select_service_identica'
			$tp = new To_Pingfm($msg);

			$usr_api=get_option('schmie_tw_ping_fm_user_api');
			$api = get_option('schmie_tw_ping_fm_api_key');	
		
			$tp->talk_via_curl($usr_api, $api);
}

//fi


		if (get_option('schmie_tw_select_service_identica')=='schmie_tw_select_service_identica'||
get_option('schmie_tw_select_service_identica')=='true'){ // == 'schmie_tw_select_service_identica'
			$tc = new To_Identica($msg);
			$ic_usr= get_option('schmie_tw_identica_usr');
			$ic_pwd= get_option('schmie_tw_identica_pwd');
			$tc->talk_via_curl($ic_usr,$ic_pwd);
				
	 
		}//TW
	
		if (get_option('schmie_tw_select_service_twitter')== 'schmie_tw_select_service_twitter'||
get_option('schmie_tw_select_service_twitter')== 'true'){
 
		
	
			$selected_service =get_option('schmie_tw_select');		
			switch ($selected_service){
				case 'Twitter':	
					$oauthInfo=$this->getAdtOauthInfo('twitter');					
					$oauth= new OAuth($oauthInfo);
					$tw = new To_Twitter($msg,$oauth);
					$tw->tweet_via_curl();
					break;
				case 'Twittermail':
					$email=get_option('schmie_tw_twittermail');
					$tw->tweet_via_email($email);
					break;
			}//switch
		}//identica
	}//publish_message

	public function short_url($url) {

		
		
		$usr = get_option('schmie_tw_bitly_usr');
		$api = get_option('schmie_tw_bitly_api');
		$selected=get_option('schmie_tw_shortener_select');				
		$services = array(
		'is.gd'=>'CIsgd',
		'tinyurl.com'=>'CTinyurl',
		'bit.ly'=>'CBitly',
		'i2h.de'=>'CI2h'			 		
		);
		$us = ShortenerInterface::factory($services[$selected], $usr, $api);
		return $us->get_short_url($url);
	 
	}//function

	
	private function fill_with_default_value($option,$default_value) {
		/**Hey, when the Plugin is installed, all settings are empty. 
		 * This function helps to fill them with default values :) 
		 */		
		if (get_option ($option) =='') 
					update_option($option, $default_value);
						
	}
	public function register_mysettings() {
		//Register the Settings
		register_setting( 'schmie_settings_group', 'schmie_tw_user' );
		register_setting( 'schmie_settings_group', 'schmie_tw_pass' );
		register_setting( 'schmie_settings_group', 'schmie_tw_twittermail' );
		register_setting( 'schmie_settings_group', 'schmie_tw_select' );
		register_setting( 'schmie_settings_group','schmie_tw_shortener_select');
		register_setting( 'schmie_settings_group',"schmie_tw_select_dont_shorten");
		register_setting( 'schmie_settings_group','schmie_tw_bitly_api');
		register_setting( 'schmie_settings_group','schmie_tw_bitly_usr');
		register_setting( 'schmie_settings_group','schmie_tw_onnew');
		register_setting( 'schmie_settings_group','schmie_tw_format_new');
		register_setting( 'schmie_settings_group','schmie_tw_onupdate');
		register_setting( 'schmie_settings_group','schmie_tw_format_update');
		register_setting( 'schmie_settings_group','schmie_tw_identica_usr');
		register_setting( 'schmie_settings_group','schmie_tw_identica_pwd');
		register_setting( 'schmie_settings_group','schmie_tw_select_service_identica');
		register_setting( 'schmie_settings_group','schmie_tw_select_service_twitter');
		register_setting( 'schmie_settings_group','schmie_tw_select_service_pingfm');
		register_setting( 'schmie_settings_group','schmie_tw_ping_fm_user_api');
		register_setting( 'schmie_settings_group','schmie_tw_ping_fm_api_key');
		
		
		
		/*******OAUTH************/
		register_setting( 'schmie_settings_oauth_group','schmie_tw_oauth_screen_name');
		register_setting( 'schmie_settings_oauth_group','schmie_tw_oauth_token');
		register_setting( 'schmie_settings_oauth_group','schmie_tw_oauth_token_secret');
		register_setting( 'schmie_settings_group','schmie_tw_verifer');
		
		/*******OAUTH************/
		//fill with default settings
		
		$this->fill_with_default_value('schmie_tw_user', 'user');
		$this->fill_with_default_value('schmie_tw_select', 'Twitter');
		$this->fill_with_default_value('schmie_tw_shortener_select', 'tinyurl.com');
	#	$this->fill_with_default_value('schmie_tw_onnew', 'true');
	#	$this->fill_with_default_value('schmie_tw_onupdate', 'true');
		$this->fill_with_default_value('schmie_tw_format_new', 'Hey there\'s a new Post on %blog: %title. Visit %url');
		$this->fill_with_default_value('schmie_tw_format_update', '%date UPDATE: %blog: %title. Visit %url');
		#$this->fill_with_default_value('schmie_tw_select_service_identica', 'true');
		#$this->fill_with_default_value('schmie_tw_select_service_twitter', 'true');

		
		
	
	}//function

	/**************************Error Handling **/
	private function write_debug($setting){
	/**	on post, I'm unable to echo something (header modifying)
		so far I write into a textfile
	**/			
	$datei= fopen(dirname(__FILE__)."/error_output","r+");
	fwrite($datei,$setting);
        fclose($datei);


	}//function	
	
	private function add_error($msg) {
		array_push($this->err_arr,$msg);
	}#function

	public function get_errors($echo="true") {
		/**
		 * @param: if $echo == false, the echoes are written in a text file
		 * 
		 */
		foreach($this->err_arr as $error)
			echo $error."<p>";
	}//function
	public function has_errors() {
		if (count($this->err_arr)==0)
			return false;
		return true;
	}//function	
	/**************************Error Handling **/

}//class
