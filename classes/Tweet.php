<?php
/*
TODO: clean up the my-> you
TODO: Get the url pruner from view.php into here

*/

require_once("helper.php");
require_once("db_setup.php");
require_once("BitLy.php");
require_once("accessConfig.php");

class Tweet{
	public $original_tweet;
	public $tweet_expandedurl;
	public $user;
	public $tweeted_on;
	public $has_link;
	public $tweet_id;

	private $tweet_text;
	private $article_title;
	private $short_link;
	
	private $dbconnect; 
	private $bitly;
	
	// Constructor
	public function __construct($tweet_id,$fresh_tweet,$user,$tweeted_on) {
		//Setup
		$this->dbconnect= new DB_Class();
		$this->bitly = new BitLy(Config::bitly_name, Config::bitly_token);			
	
		$this->tweet_id=$tweet_id;
		$this->original_tweet=$fresh_tweet;
		$this->user=$user;	
		$this->tweeted_on=$tweeted_on;
		$this->has_link=false;
		$this->article_title="";
		
		$parts = getParts($fresh_tweet);
		
		//for those tweets with links
		if(count($parts)>1){		
			$this->has_link = true;
			
			//setup bitly
			
			
			//stripping the vias fromt the back of retweets
			$this->tweet_text = $this->stripPrep($parts[0]);
			
			$tmpParts = explode(' ',$parts[1]);
			$shortLink = $tmpParts[0];
			$this->short_link = $shortLink;
			
			if(containsTinyUrl($shortLink)){
				if(isBitLy($shortLink)){
					//for the bitly
					//print makeLink($label,$bitly->expandUrlByUrl($finUrl));
					$this->tweet_expandedurl = stripUTM($this->bitly->expandUrlByUrl($shortLink));
				}else{
					//for the others. 
					//print makeLink($label,expandURL($finUrl));			
					$this->tweet_expandedurl = stripUTM(expandURL($shortLink));
				}
				//$this->tweet_expandedurl = "http://www.google.com";
			}else{
				//Use link as is, probably expanded by me already
				//print makeLink($label,$finUrl);
				$this->tweet_expandedurl = $shortLink;
			}
		
		}
	}
	
	public function insert_to_rawtweets($state){
		$str_id=mysql_escape_string($this->tweet_id);
		$title=mysql_escape_string($this->tweet_text);
		$text=mysql_escape_string($this->original_tweet);
		$link=mysql_escape_string($this->tweet_expandedurl);
		$user_screen_name=mysql_escape_string($this->user);
		$tweet_create_on=$this->tweeted_on;
		
		$dbconnect = new DB_Class();
		$query = "INSERT INTO `newsconverter`.`rawtweets` "
		."(`str_id`, `title`, `text`, `link`, `user_screen_name`, `tweet_create_on`,`createon`, `state`) "
		."VALUES "
		."('$str_id', '$title', '$text', '$link', '$user_screen_name', '$tweet_create_on',NOW(), '$state');";
		//echo $query;
		$dbconnect->query($query);				
	}
	
	public function spin(){
		//Check for "I " do x may have issue 
		
		
		return $this->get_title()." ".$this->bitly->shortenUrl($this->tweet_expandedurl);
	
	}
	
	public function get_title(){
		if($this->article_title=="") return $this->tweet_text;
		return $this->article_title;
	
	}
	
	public function insert_content(){
		try{
			$sXML = download_page("http://api.thequeue.org/v1/clear?url=".$this->tweet_expandedurl);
			$oXML = new SimpleXMLElement($sXML);
			$content = mysql_escape_string($oXML->channel->item->description);	
			$titleFromXML = mysql_escape_string($oXML->channel->item->title);
			$this->article_title = $oXML->channel->item->title;
			$tweetTitle = mysql_escape_string($this->tweet_text);
			$link = mysql_escape_string($this->tweet_expandedurl);
			
			insertToContentLinks($tweetTitle,$titleFromXML,$this->short_link,$link,$content);
			updateRawTweetState($this->tweet_id,"ITWEET_INSERTED");
			echo "[INSERTED_CONTENT]".$this->tweet_text."\n";
		}catch(Exception $e){
			echo "[Exception]".$e->getMessage()." on line ".$this->tweet_text."\n";
		}					
	}
	
	private function stripPrep($text){
		//rewtweet pattern text removal
		$pattern_RT = '/RT @\w+: /i';
		$ret_text = html_entity_decode(preg_replace($pattern_RT,'',$text),ENT_QUOTES, 'UTF-8');
		
		$pattern_TY = '/TY!/i';
		$ret_text = preg_replace($pattern_TY,'',$ret_text);
		return $ret_text;
	}
	

}

?>