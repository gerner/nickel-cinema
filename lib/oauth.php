<?php
//assume $params already has oauth params
//assume $params is sorted
function SignRequest($verb, $url, $params, $secretKey)
{
	$paramsArray = array();
	foreach($params as $k => $v)
	{
		$paramsArray[] = $k."=".rawurlencode($v);
	}
	$parameters = implode("&", $paramsArray);
	
	$stringToSign = "$verb&".urlencode($url)."&".urlencode($parameters);
	$binarySignature = hash_hmac('sha1', $stringToSign, $secretKey, true);
	// We need to base64-encode it and then url-encode that.
	$urlSafeSignature = urlencode(base64_encode($binarySignature));
	
	return $urlSafeSignature;
}

//taken from http://www.lost-in-code.com/programming/php-code/php-random-string-with-numbers-and-letters/
function genRandomString($length=10) {
    $characters = ‘0123456789abcdefghijklmnopqrstuvwxyz’;
    $string = ”;    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}

//does not assume $params already has oauth params
//does not assume $params is sorted
function GenerateOAuthRequest($verb, $url, $params, $id, $secretKey)
{
	$params["oauth_consumer_key"] = $id;
	$params["oauth_nonce"] = genRandomString();
	$params["oauth_signature_method"] = "HMAC-SHA1";
	$params["oauth_timestamp"] = mktime();
	$params["oauth_version"] = "1.0";
	
	ksort($params);
	$signature = SignRequest($verb, $url, $params, $secretKey);
	
	$paramsArray = array();
	foreach($params as $k => $v)
	{
		$paramsArray[] = $k."=".rawurlencode($v);
	}
	$parameters = implode("&", $paramsArray);
	
	return "$url?$parameters&oauth_signature=$signature";
}
?>