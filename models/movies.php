<?php
class Movies
{
	public $movies;
	public $zipcode;
	
	function __construct($zipcode)
	{
		$this->zipcode = $zipcode;
		$this->movies = NULL;
	}
	
	function load()
	{
		//using this: http://hurwi.net/blog/?p=17
		//curl --referer "http://hurwi.net/map/" "http://hurwi.net/map/parser2xml.php?loc=98116"
		// create a new cURL resource
		$ch = curl_init();
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, "http://hurwi.net/map/parser2xml.php?loc=".$this->zipcode);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, "http://hurwi.net/map/");
		
		$moviesResponse = curl_exec($ch);
		curl_close($ch);
		
		echo "<!--\n";
		echo $moviesResponse;
		echo "\n-->";
		
		//document structured like:
		//- theaters
		//	- theater
		//		- theatername
		//		- movies
		//			- movie
		//				- name
		//				- showtimes
		//					- showtime
		//					- showtime
		//			- movie
		//	- theater
		
		//want to create objects like:
		//- Movie
		//	- Name of the movie
		//	- Theaters showing this movie
		//		- Theater : showtimes at that theater
		//		- Theater : showtimes at that theater
		
		$domDoc = new DOMDocument('1.0', 'UTF-8');
		$domDoc->loadXML($moviesResponse);
		$showtimes = $domDoc->getElementsByTagName("Times");
		
		$movies = array();
		
		foreach($showtimes as $showtime)
		{
			$movie = $showtime->parentNode;
			//get the name of the movie
			$movieName = NULL;
			foreach($movie->childNodes as $child)
			{
				if($child->nodeName == "MovieName")
				{
					$movieName = $child->nodeValue;
					break;
				}
			}
			$theater = $movie->parentNode->parentNode;
			//get the name of the theater
			$theaterName = NULL;
			foreach($theater->childNodes as $child)
			{
				if($child->nodeName == "TheaterName")
				{
					$theaterName = $child->nodeValue;
					break;
				}
			}
			
			//make sure we've got the movie
			if(!$movie = $movies[$movieName])
				$movie = $movies[$movieName] = new Movie($movieName);
			
			$times = preg_split("/[\s]+/", $showtime->nodeValue);
			$times_r = array_reverse($times);
			$numTimes = count($times);
			foreach($times_r as $k => $time)
			{
				$postfixCandidate = substr($time, -2); 
				if($postfixCandidate == "am")
				{
					$times[$numTimes - $k - 1] = $time;
					$postfix = "am";
				}
				elseif($postfixCandidate == "pm")
				{
					$times[$numTimes - $k - 1] = $time;
					$postfix = "pm";
				}
				else
				{
					$time .= $postfix;
					$times[$numTimes - $k - 1] = $time;
				}
			}
			$theater = $movie->theaters[$theaterName] =  $times;
		}
		$this->movies = $movies;
		
		Movie::load_many($this->movies);		
	}
} 
?>