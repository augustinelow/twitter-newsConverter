<?php
 
class BitLy {
	private $break;
	private $api_version;
	private $format;
	private $login;
	private $apikey;
 
	// Constructor for BitLy class
	public function __construct($login, $apikey) {
		// Browser output then use "<br />"
		// Command line output use "\n" 
		$this->break = "\n";
		$this->api_version = "2.0.1";
		$this->format = "json";
		$this->login = $login;
		$this->apikey = $apikey;
	}
 
	// Shorten given url and returns shortened url
	public function shortenUrl($url) {
		$shortened_url = "";
		$encoded_url = urlencode($url);
		$bitly_url = "http://api.bit.ly/shorten?" . 
				"version=" . $this->api_version . 
				"&format=" . $this->format . 
				"&longUrl=" . $encoded_url . 
				"&login=" . $this->login . 
				"&apiKey=" . $this->apikey;
 
		$content = file_get_contents($bitly_url);
 
		try {
			$shortened_url = $this->parseContent($content, $url);
 
		}
		catch (Exception $e) {
			echo "Caught exception: " . 
				$e->getMessage() . $this->break;
			exit;
		}
 
		return $shortened_url;
	}
 
	// Expand given url and returns expanded url
	public function expandUrlByUrl($url) {
		$expanded_url = "";
 
		$hash = $this->parseBitlyUrl($url);
 
		$expanded_url = $this->expandUrlByHash($hash);
 
		return $expanded_url;
	}
 
	// Expand given hash and returns expanded url
	public function expandUrlByHash($hash) {
		$expanded_url = "";
		$bitly_url = "http://api.bit.ly/expand?" . 
				"version=" . $this->api_version . 
				"&format=" . $this->format . 
				"&hash=" . $hash . 
				"&login=" . $this->login . 
				"&apiKey=" . $this->apikey;
 
		$content = file_get_contents($bitly_url);
 
		try {
			$expanded_url = $this->parseContent($content, $hash);
		}
		catch (Exception $e) {
			echo "Caught exception: " . 
				$e->getMessage() . $this->break;
			exit;
		}
 
		return $expanded_url;
	}
 
	// Parse Bitly url returns bitly hash
	private function parseBitlyUrl($url) {
		$parsed_url = parse_url($url);
		return trim($parsed_url['path'], "/");
	}
 
	// Parse Content from Bitly API
	private function parseContent($content, $key) {
		// Decode json to array
		$content = json_decode($content, true);
 
		// Check errors
		if ($content['errorCode'] != 0 || 
		    $content['statusCode'] != "OK") {
			throw new Exception($content['statusCode'] . ": " . 
					$content['errorCode'] . " " . 
					$content['errorMessage']);
		}
 
		// Return right url or throw exception if not set
		if (isset($content['results'][$key]['longUrl'])) {
			return $content['results'][$key]['longUrl'];
		}
		else if (isset($content['results'][$key]['shortUrl'])) {
			return $content['results'][$key]['shortUrl'];
		}
		else {
			throw new Exception("ERROR. URL not found: " . $key);
		}
	}
 
}
 
?>