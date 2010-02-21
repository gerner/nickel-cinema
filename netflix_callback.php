<?php
require_once("includes.php");
$_skip_netflix = true;
require_once("lib/session.php");
//handles the netflix login callback

$oauth_token_secret = $_SESSION["oauth_token_secret"];

//formulate the request for the authorized token and secret:
$url = "http://api.netflix.com/oauth/access_token";
$params = array("oauth_token" => $_GET["oauth_token"]);

$requestURL = GenerateOAuthRequest("GET", $url, $params, NETFLIX_CONSUMER_KEY, NETFLIX_SHARED_SECRET."&".$oauth_token_secret);
//echo $requestURL ."<br />";

$ch = curl_init();
		
// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, $requestURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);
curl_close($ch);

//echo $response;

$responseArray = explode("&", $response);
$responseMap = array();
foreach($responseArray as $pair)
{
	$pairArray = explode("=", $pair);
	$responseMap[$pairArray[0]] = urldecode($pairArray[1]);
}

$_SESSION["oauth_token"] = $responseMap["oauth_token"];
$_SESSION["user_id"] = $responseMap["user_id"];
$_SESSION["oauth_token_secret"] = $responseMap["oauth_token_secret"];
header("Location: ".NICKEL_CINEMA_ROOT);
?>