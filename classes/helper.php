<?php
require_once("db_setup.php");

function makeLink2($label,$link){
	//trim link of ?utm
	$parts = explode('?', $link);
	return "<li><a href='".$parts[0]."'>".$label."</a></li>";
}

function stripUTM($link){
	$parts = explode('?', $link);
	if((count($parts)>1)&&(strpos($parts[1],"utm_source")!==false))
		return $parts[0];	
		
	return $link;
}

function getTweetUserNameByLink($link){
	$dbconnect = new DB_Class();
	$query = "select title,text,user_screen_name from rawtweets where link ='$link'";
	return  $dbconnect->query($query);

}

function isUserBanned($user){
	$dbconnect = new DB_Class();
	$query = "select count(*) as count from `mutedusers` where username='$user';";
	$results = $dbconnect->query($query);
	$row=mysql_fetch_array($results);
	if($row["count"]==0)return false;
	return true;
}

function isUserBroadcast($user){
	$dbconnect = new DB_Class();
	$query = "select count(*) as count from `broadcastusers` where username='$user';";
	$results = $dbconnect->query($query);
	$row=mysql_fetch_array($results);
	if($row["count"]==0)return false;
	return true;
}

function getLastProcessedTweetID(){	
	$dbconnect = new DB_Class();
	$query = "select * from rawtweets order by tweet_create_on  desc";
	$row = $dbconnect->getone($query);
	return $row[0];		
}

function updateLastProcessedTweetID($newID,$oldID){
	$query = "update lastid set lastid='$newID' where lastid='$oldID';";
	$dbconnect = new DB_Class();
	$dbconnect->query($query);	
}

function getCategoryColor($category){
	if($category=="Programming")
		return "red";
	if($category=="Marketing")
		return "blue";
	if($category=="Entertainment")
		return "green";
	if($category=="News")
		return "yellow";	
	if($category=="Design")
		return "black";	
}

function makeLink($label,$link){
	//trim link of ?utm
	$parts = explode('?', $link);
	if((count($parts)>1)&&(strpos($parts[1],"utm")!==false))
		return "<li>".$label ." <a href='".$parts[0]."'>".$parts[0]."</a></li>";	
		
	return "<li>".$label ." <a href='".$link."'>".$link."</a></li>";
}

function insertToContentLinks($tweetTitle,$title,$shortLink,$link,$content){
	$dbconnect = new DB_Class();
	$query = "INSERT INTO `newsconverter`.`contentlink` "
	."(`id`, `tweet_title`, `articleTitle`, `oldLink`, `link`, `category`, `contentBody`, `createon`) "
	."VALUES "
	."(NULL, '$tweetTitle','$title','$shortLink','$link', NULL, '$content', now());";	
	$dbconnect->query($query);
}

function updateRawTweetState($str_id,$state){
	$dbconnect = new DB_Class();
	$query = "update `newsconverter`.`rawtweets` "
	."set `state`='$state' "
	."where str_id='$str_id';";
	//echo $query;
	$dbconnect->query($query);		
}

function isLinkExists($link){
	$dbconnect = new DB_Class();
	$query = "SELECT count(*) as count FROM `contentlink` where link like '%".$link."%'";
	$results = $dbconnect->query($query);
	$row=mysql_fetch_array($results);
	if($row["count"]==0)return false;
	return true;
	
}


function download_page($path){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$path);
		curl_setopt($ch, CURLOPT_FAILONERROR,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		$retValue = curl_exec($ch);                      
		curl_close($ch);
		return $retValue;
}

function getParts($line_of_text){
	$parts = explode('http://', $line_of_text);
	$protocol_prefix= "http://";
	if(count($parts)==1){
		$parts=explode('https://', $line_of_text);
		$protocol_prefix= "https://";
	}
	if(count($parts)>1){
		$parts[1]=$protocol_prefix.trim($parts[1]);
	}
	return $parts;
}

function makeHeader($label){
	$parts = explode('][', $label);	
	return "<h2>".$parts[0]."</h2><em>".$parts[1]."</em>";
}

function isHeader($text){
	if(strpos($text,"][")!==false){
		return true;
	}
	return false;
}

function containsTinyUrl($url){
	if(strpos($url,"t.co")!==false){
		return true;
	}else if(strpos($url,"tinyurl")!==false){
		return true;
	}else if(strpos($url,"j.mp")!==false){
		return true;
	}else if(strpos($url,"bit.ly")!==false){
		return true;
	}
	return false;
}

function containshttp($url){
	if(strpos($url,"http")!==false)
		return true;
	return false;
}

function isBitLy($url){
	if(strpos($url,"bit.ly")!==false)
		return true;
	return false;
}

function expandURL($url)
{
    $retVal = 'Error';
	$isretError = false;	
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    if(curl_exec($ch) != false)
    {
        $response = curl_exec($ch);
        if($response != false)
        {
            $responseInfo = curl_getinfo($ch);
            if($responseInfo['http_code'] == 200)
            {
                $retVal =  $responseInfo['url'];
            }
            else if($responseInfo['http_code'] == 404)
            {
                $retVal =  'URL Not found';				
				$isretError = true;
            }
            else
            {
                $retVal =  'HTTP error: '.$responseInfo['http_code'];
				$isretError = true;
            }
        }
        else
        {
            $retVal =  curl_error($ch);
			$isretError = true;

        }
    }
    else
    {
        $retVal =  'cURL error ocurred : '.curl_error($ch);
		$isretError = true;

    }
    curl_close($ch);
	if($isretError)
		return $url;
    return $retVal;
}
?>