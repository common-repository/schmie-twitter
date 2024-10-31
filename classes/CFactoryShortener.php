<?php
#error_reporting( -1 );
/**
@date: 14.04.10
@author: schmiddi, schmiddim@gmx.at,
@desc: 	A class for interacting with different shortening services.
	Supported Services:
	is.gd:	http://is.gd
	bit.ly:	http://bit.ly/
	i2h:	http://i2h.de
	tinyurl:http://tinyurl.com/
	It uses now the Factory Pattern. Nice
	
*/
abstract class ShortenerInterface{
	//some attributes
	protected  $login, $apikey,$serviceurl,$longurl;
		
	protected function __construct($login="", $apikey=""){
		$this->login = $login;
		$this->apikey = $apikey;
	}
	
	public static function factory($class,$login="", $apikey=""){	
		return new $class($login=$login, $apikey=$apikey);
											
	}//factory
	
	protected function get_url($longurl) {
		if( substr($longurl,0,7) !="http://")
			$longurl="http://".$longurl;
		$url=$this->serviceurl . urlencode($longurl);
		$curl_handle = curl_init();			
		curl_setopt($curl_handle, CURLOPT_URL, "$url");
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_POST, 1);		
		$retval =  curl_exec($curl_handle);
		curl_close($curl_handle);	
		return $retval;
	}//get_url
	
	//just implement this function in every normal class
	public abstract function get_short_url($url);
	
}//class

class CTinyurl extends ShortenerInterface{	
	public function get_short_url($longurl){			
		$this->serviceurl='http://tinyurl.com/api-create.php?url=';
		return $this->get_url($longurl);		
	}//function
}//class

class CIsgd extends ShortenerInterface{
	public function get_short_url($longurl){			
		$this->serviceurl='http://is.gd/api.php?longurl=';
		return $this->get_url($longurl);		
	}//function	
}//class

class CI2h extends ShortenerInterface{
	public function get_short_url($longurl){			
		$this->serviceurl='http://api.i2h.de/v1/?method=shrink&url=';
		return $this->get_url($longurl);		
	}//function	
}//class

class CBitly extends ShortenerInterface{
	public function get_short_url($longurl){		
		$resturl= 'http://api.bit.ly/shorten?version=2.0.1&login=%s&apiKey=%s&longUrl=';	
		sprintf($resturl, $this->login, $this->apikey);
		$this->serviceurl=sprintf($resturl, $this->login, $this->apikey);	
		$result = json_decode( $this->get_url($longurl));
		if ($result->errorCode==0){			
			if( substr($longurl,0,7) !="http://")
				$longurl="http://".$longurl;
    		return  $result->results->$longurl->shortUrl;
		} else {
			$this->add_error($result->errorMessage);

		}//fi
	}//function	
}//class


/***** Testen **********/
/*
$services = array('CIsgd', 'CTinyurl', 'CI2h', 'CBitly');
foreach ($services as $service){
	$iface = ShortenerInterface::factory($service,   "user", "api");
	$url =  $iface->get_short_url("schmiddi.co.cc");
	echo "<a href=\"$url\">$url</a><br>";	
	
}//each
*/

?>