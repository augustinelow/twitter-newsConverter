<?php
/*
TODO: create a rotary chain to go through different twitter profiles and fire off the tweets from there
TODO: muffle the I' and my

*/
require_once('twitteroauth/twitteroauth.php');
require_once('twitteroauth/config.php');
require_once("classes/helper.php");
require_once("classes/Tweet.php");
require_once("classes/accessConfig.php");

$access_token = array(
		'oauth_token' => Config::twitter_oauth_token,
		'oauth_token_secret' => Config::twitter_token_secret,
		'user_id' => Config::twitter_userid,
		'screen_name' => Config::twitter_screenname
	);

//starts a new twitter oauted connx	
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

/* If method is set change API call made. Test is called by default. */
//for testing
//$content = $connection->get('account/verify_credentials');
//print_r($content);

$old_processed_id = getLastProcessedTweetID(); 

$raw_tweets = $connection->get('statuses/home_timeline', array('count'=>'200','since_id'=>$old_processed_id));
//$raw_tweets = $connection->get('statuses/home_timeline', array('count'=>'10'));
//echo "<pre>";
//print_r($raw_tweets);
//echo "</pre>";

$new_processed_id = $raw_tweets[0]->id_str; 
print "[START ID]:$old_processed_id\n";

foreach($raw_tweets as $obj){
	$curr_tweet = new Tweet(
		$obj->id_str,
		$obj->text,
		$obj->user->screen_name,
		date("Y-m-d G:i:s", strtotime($obj->created_at))
		);
	
	if($curr_tweet->has_link&&!isUserBanned($curr_tweet->user)){
		$tweet = $curr_tweet->original_tweet;
		if(isUserBroadcast($curr_tweet->user)&&!isLinkExists($curr_tweet->tweet_expandedurl)){
			$connection->post('statuses/update', array('status' => $curr_tweet->spin()));			
			echo "[TWEETED]:$tweet\n";						
			$curr_tweet->insert_to_rawtweets("ITWEET");
			//get it across to contentLinks
			$curr_tweet->insert_content();
		}else{
			//$curr_tweet->insert_to_rawtweets("FRESH");
			//echo "[INSERTED]:$tweet\n";
			//USED to insert fresh now we ignore, saves space and gives sanity
		}
	
	}

}

//updateLastProcessedTweetID($new_processed_id,$old_processed_id);
print "[END ID]:$new_processed_id\n";

?>
