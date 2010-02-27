<?php
/****
 * makes sure the current user has already authorized us with netflix
 */
session_name(NICKEL_CINEMA_SESSION);
if(NICKEL_CINEMA_SESSION_LIFETIME >= 0) session_set_cookie_params(NICKEL_CINEMA_SESSION_LIFETIME);
session_start();

if(!$_SESSION["oauth_token"] && !$_skip_netflix && !SPOOF_DATA)
{
	$requestURL = GenerateOAuthRequest("GET", "http://api.netflix.com/oauth/request_token", array(), NETFLIX_CONSUMER_KEY, NETFLIX_SHARED_SECRET."&");
	$ch = curl_init();
		
	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, $requestURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$response = curl_exec($ch);
	curl_close($ch);
	
	$responseArray = explode("&", $response);
	$responseMap = array();
	foreach($responseArray as $pair)
	{
		$pairArray = explode("=", $pair);
		$responseMap[$pairArray[0]] = urldecode($pairArray[1]);
	}
	
	$_SESSION["oauth_token_secret"] = $responseMap["oauth_token_secret"];
	
	$loginURL = "https://api-user.netflix.com/oauth/login?application_name=".urlencode($responseMap["application_name"]);
	$loginURL .= "&oauth_callback=".urlencode(NICKEL_CINEMA_ROOT."netflix_callback.php");
	$loginURL .= "&oauth_consumer_key=".NETFLIX_CONSUMER_KEY;
	$loginURL .= "&oauth_token=".$responseMap["oauth_token"];
	
	header("location: $loginURL");
	die();
} 
?>