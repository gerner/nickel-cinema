<?php 
class Movie
{
	public $name;
	public $theaters;
	public $rating;
	public $netflixInfoURL;
	public $netflixRefURL;
	
	function __construct($name)
	{
		$this->name = $name;
		$this->theaters = array();
		$this->rating = NULL;
	}
	
	function netflixTitleURL()
	{
		$secretKey = "jKtfhedxG2&";
		$method = "GET";
		$url = "http://api.netflix.com/catalog/titles";
		$params = array("term" => $this->name, "max_results" => 1);
		
		return GenerateOAuthRequest("GET", $url, $params, NETFLIX_CONSUMER_KEY, NETFLIX_SHARED_SECRET."&");
	}
	
	function parseNetflixTitleResponse($response)
	{
		$domDoc = new DOMDocument('1.0', 'UTF-8');
		$domDoc->loadXML($response);
		$xpath = new DOMXPath($domDoc);
		$this->netflixRefURL = $xpath->query("//id")->item(0)->nodeValue;
		$this->netflixInfoURL = $xpath->query("//link[@title='web page']")->item(0)->attributes->getNamedItem("href")->nodeValue;
	}
	
	function loadTitle()
	{
		if(($titleRef = cache_get("netflix_title_ref_".$this->name)) &&
			($titleWeb = cache_get("netflix_title_web_".$this->name)))
		{
			$this->netflixRefURL = $titleRef;
			$this->netflixInfoURL = $titleWeb;
		}
		else
		{
			$curlcode = 0;
			$tries = 0;
			while($curlcode != 200)
			{
				$ch = curl_init();
				$requestURL = $this->netflixTitleURL();
				curl_setopt($ch, CURLOPT_URL, $requestURL);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch);
				$curlcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				$tries++;
				if($tries > 2)
					die("could not get title info for \"".$this->title."\" from netflix");
				if($curlcode == 403)
					sleep(1);
			}
			
			$this->parseNetflixTitleResponse($response);
			cache_set("netflix_title_ref_".$this->name, $this->netflixRefURL);
			cache_set("netflix_title_web_".$this->name, $this->netflixInfoURL);
		}
	}
	
	function parseNetflixRatingResponse($response)
	{
		$domDoc = new DOMDocument('1.0', 'UTF-8');
		$domDoc->loadXML($response);
		$xpath = new DOMXPath($domDoc);
		$this->rating = $xpath->query("//predicted_rating")->item(0)->nodeValue;
	}
	
	function load()
	{	
		//first get metadata about the netflix title
		loadTitle();
		
		//now get user rating!
		$curlcode = 0;
		while($curlcode != 200)
		{
			$ch = curl_init();
			$url = "http://api.netflix.com/users/".$_SESSION["user_id"]."/ratings/title/predicted";
			$params = array("oauth_token" => $_SESSION["oauth_token"], "title_refs" => $this->netflixRefURL);
			$requestURL = GenerateOAuthRequest("GET", $url, $params, NETFLIX_CONSUMER_KEY, NETFLIX_SHARED_SECRET."&".$_SESSION["oauth_token_secret"]);
			$ch = curl_init();
					
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $requestURL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$response = curl_exec($ch);
			$curlcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			$tries++;
			if($tries > 2)
				die("could not get ratings info for \"".$this->title."\" from netflix");
			if($curlcode == 403)
				sleep(1);
		}
		
		$this->parseNetflixRatingResponse($response);
	}
	
	static function load_many($movies)
	{
		$titleRefs = array();
		foreach($movies as $movie)
		{
			$movie->loadTitle();
			$titleRefs[] = $movie->netflixRefURL;
		}
		
		//now get user ratings!
		$curlcode = 0;
		while($curlcode != 200)
		{
			$ch = curl_init();
			$url = "http://api.netflix.com/users/".$_SESSION["user_id"]."/ratings/title/predicted";
			$params = array("oauth_token" => $_SESSION["oauth_token"], "title_refs" => implode(",", $titleRefs));
			$requestURL = GenerateOAuthRequest("GET", $url, $params, NETFLIX_CONSUMER_KEY, NETFLIX_SHARED_SECRET."&".$_SESSION["oauth_token_secret"]);
			$ch = curl_init();
					
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $requestURL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$response = curl_exec($ch);
			$curlcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			$tries++;
			if($tries > 2)
				die("could not get ratings info from netflix");
			if($curlcode == 403)
				sleep(1);
		}
		$domDoc = new DOMDocument('1.0', 'UTF-8');
		$domDoc->loadXML($response);
		$xpath = new DOMXPath($domDoc);
		foreach($movies as $movie)
		{
			$movie->rating = $xpath->query("//link[@href='".$movie->netflixRefURL."']/../predicted_rating")->item(0)->nodeValue;
		}
	}
}?>