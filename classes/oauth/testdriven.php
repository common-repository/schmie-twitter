<?php 
error_reporting(-1);
require_once('helpers.php');

	$endpoint='http://term.ie/oauth/example/request_token.php';
	$auth = new OAuth("key", "secret");
	$auth->setRequestTokenEndpoint('http://term.ie/oauth/example/request_token.php');
	$arguments = $auth->requestToken(array('oauth_callback'=>'http://www.localhost.de'));
	$url = $auth->generatetUrl($arguments, $endpoint);
	echo"<a href=\"$url\">REQUEST TOKEN</a><p>";

//access token------------------------------------------------------------------------------
	$auth->setAccessTokenEndpoint('http://term.ie/oauth/example/access_token.php');
	$arguments=$auth->requestAccessToken();
	$endpoint='http://term.ie/oauth/example/access_token.php';
	$url = $auth->generatetUrl($arguments, $endpoint);
	echo"<a href=\"$url\">ACCESS TOKEN</a><p>";

//MAKING AUTHENTICATED CALLS-----------------------------------------------------
//oauth_token=accesskey&oauth_token_secret=accesssecret'
	$endpoint='http://term.ie/oauth/example/echo_api.php';
	$auth->setApiEndpoint($endpoint);
	$params =array ("ey" =>  " theumläaut");
	$arguments=$auth->accessApi($params);
	$url = $auth->generatetUrl($arguments, $endpoint);	
	echo"<a href=\"$url\">ACCESS API</a><p>";


?>