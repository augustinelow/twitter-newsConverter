<?php
require_once("classes/BitLy.php");
require_once("classes/helper.php");
require_once("classes/db_setup.php");

$query = "SELECT * FROM `rawtweets` where state='ITWEET' order by tweet_create_on asc";
$dbconnect = new DB_Class();
$results = $dbconnect->query($query);

$linkCount=0;
while($row = mysql_fetch_array($results)){
	$longLink= $row["link"];
	$shortLink = $longLink;
	if(!isLinkExists($longLink)){
		try{
			$sXML = download_page("http://api.thequeue.org/v1/clear?url=".$longLink);
			$oXML = new SimpleXMLElement($sXML);
			$content = mysql_escape_string($oXML->channel->item->description);	
			$titleFromXML = mysql_escape_string($oXML->channel->item->title);
			$tweetTitle = mysql_escape_string($row["title"]);
			$link = mysql_escape_string($longLink);
			
			insertToContentLinks($tweetTitle,$titleFromXML,$shortLink,$link,$content);
			updateRawTweetState($row["str_id"],"ITWEET_INSERTED");
			print "[INSERTED]".$row["text"]."\n";
		}catch(Exception $e){
			print "[Exception]".$e->getMessage()." on line ".$row["text"]."\n";
		}
		$linkCount++;				
	}else{
		print "[REPEATED LINK]:".$row["text"]."\n";	
	}					
		
	
}
print "Total Links inserted : $linkCount";
?>