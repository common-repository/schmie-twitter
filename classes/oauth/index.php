<?php
error_reporting(-1);
require_once('helpers.php');
 
//setting up the ADT
$oauthInfo = new AdtOauthInfo();
$oauthInfo->oauth_consumer_key = '7ORCIeptKqcgPmt8Lcpgpw';
$oauthInfo->oauth_consumer_secret = '0cN8nu7TF1BijQHOco7VyROoGJ92vV0LMUgSEXZggk';
$oauthInfo->endpoint_request_token = 'https://api.twitter.com/oauth/request_token';
$oauthInfo->endpoint_access_token = 'https://api.twitter.com/oauth/access_token';
$oauthInfo->endpoint_authorize='https://api.twitter.com/oauth/authorize';
$oauthInfo->oauth_callback='oob';
session_start();
#session_destroy();
//RequestToken
if (!isset($_GET['oauth_verifier'])){
	echo "IN";
	$auth = new OAuth($oauthInfo);
	$auth->setMethod("POST");
	$oauthInfoNew=	$auth->requestToken();

	//send user to Service Provider
	if ($oauthInfoNew->oauth_token!=''){
		$_SESSION['oauthInfo'] = serialize($oauthInfoNew);	
		$authUrl= $oauthInfoNew->generateAuthorizeUrl();		
		echo "<p><a href=\"$authUrl\">klick</a><p>";
	}//fi
}//fi

if (isset($_GET['oauth_verifier'])){
	$oauthInfo = unserialize($_SESSION['oauthInfo']);	
	$oauthInfo->oauth_verifier =$_GET['oauth_verifier'];
	$auth = new OAuth($oauthInfo);
	echo "<p>___________________________________________________<p>";
	$oauthInfoNew=$auth->requestAccessToken();
	$_SESSION['oauthInfo'] = serialize($oauthInfoNew);
	
	#	echo $oauthInfo->oauth_token;	

		/*echo "IN";
	 
	 $oauthInfo->oauth_token=$_SESSION['oauth_token'];
	$auth = new OAuth($oauthInfo);
	$auth->requestAccessToken()	;
	*/
}//fi

print_r($_SESSION);
if (isset($_SESSION['oauthInfo'])){
	$oauthInfo = unserialize($_SESSION['oauthInfo']);
	if ($oauthInfo->screen_name!=''){
		echo "work is done";
		$endpoint ='http://api.twitter.com/1/statuses/update.json';
		$auth=new OAuth($oauthInfo);
		$payload = array ("status"=>"the work is done:)");
		$auth->accessApi($payload, $endpoint);
	}
	
	echo ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>;";
}
?>
<form action="index.php" method ="get">
<label for="oauth_verifier">Enter the Pin</label>
<input type="text" size="15" name="oauth_verifier"></input>
<input type="submit" value="OK"></input>
</form>
<?php 
	
#session_start();
/*
if (!isset($_GET['oauth_verfier'])){
	$auth = new OAuth($oauthInfo);
	$auth->setMethod("POST");
	$oauth= $auth->requestToken();
	echo "<a href=\"{$auth->makeAuthorizeUrl()}\">klick</a>";
	$_SESSION['oauth_token']=$oauthInfo->oauth_token; 


}
if (isset($_GET['oauth_verfier'])){
	echo "IN";
	 $oauthInfo->oauth_verifier = $_GET['oauth_verfier'];
	 $oauthInfo->oauth_token=$_SESSION['oauth_token'];
	$auth = new OAuth($oauthInfo);
	$auth->requestAccessToken()	;
} */


//-------------------running! access the api
/*$endpoint ='http://api.twitter.com/1/statuses/update.json';
$auth=new OAuth($oauthInfo);
$payload = array ("status"=>"still working:)");
$auth->accessApi($payload, $endpoint);
*/


#session_destroy();

//hey, do we hve the token?
/*
if (isset($_SESSION['has_token']) &&$_SESSION['has_token']){
	echo "<h1>YES WE HAVE</h1>";
	echo $_SESSION['token'] ;
echo		$_SESSION['token_secret'];
;
}else{	
	$auth->setMethod("POST");
	//fetched a token?
	if($auth->requestToken()){
		$_SESSION['has_token'] = true;
		$_SESSION['token'] = $auth->getToken();
		$_SESSION['token_secret'] = $auth->getTokenSecret();
		
	
	}else {
		$_SESSION['has_token'] = false;
			
}//fi
}//if
#


*/





?>